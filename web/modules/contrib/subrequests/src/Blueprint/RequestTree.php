<?php


namespace Drupal\subrequests\Blueprint;

use Rs\Json\Pointer;
use Rs\Json\Pointer\NonexistentValueReferencedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains the hierarchical information of the requests.
 */
class RequestTree {

  const ROOT_TREE_ID = '#ROOT#';
  const SUBREQUEST_TREE = '_subrequests_tree_object';
  const SUBREQUEST_ID = '_subrequests_content_id';
  const SUBREQUEST_PARENT_ID = '_subrequests_parent_id';
  const SUBREQUEST_DONE = '_subrequests_is_done';

  /**
   * @var \Symfony\Component\HttpFoundation\Request[]
   */
  protected $requests;

  /**
   * If this tree sprouts from another requests, save the request id here.
   *
   * @var string
   */
  protected $parentId;

  /**
   * RequestTree constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request[] $requests
   * @param string $parent_id
   */
  public function __construct(array $requests, $parent_id = NULL) {
    $this->requests = $requests;
    $this->parentId = $parent_id;
  }

  /**
   * Gets a flat list of the initialized requests for the current level.
   *
   * All requests returned by this method can run in parallel. If a request has
   * children requests depending on it (sequential) the parent request will
   * contain a RequestTree itself.
   *
   * @return \Symfony\Component\HttpFoundation\Request[]
   *   The list of requests.
   */
  public function getRequests() {
    return $this->requests;
  }

  /**
   * Is this tree the base one?
   *
   * @return bool
   *   TRUE if the tree is for the master request.
   */
  public function isRoot() {
    return !$this->getParentId();
  }

  /**
   * Get the parent ID of the request this tree belongs to.
   *
   * @return string
   */
  public function getParentId() {
    return $this->parentId;
  }

  /**
   * Find all the sub-trees in this tree.
   *
   * @return static[]
   *   An array of trees.
   */
  public function getSubTrees() {
    $trees = array_map(function (Request $request) {
      return $request->attributes->get(static::SUBREQUEST_TREE);
    }, $this->getRequests());

    return array_filter($trees);
  }

  /**
   * Find a request in a tree based on the request ID.
   *
   * @param string $request_id
   *   The unique ID of a request in the blueprint to find in this tree.
   *
   * @return \Symfony\Component\HttpFoundation\Request|NULL $request
   *   The request if found. NULL if not found.
   */
  public function getDescendant($request_id) {
    // Search this level's requests.
    $found = array_filter($this->getRequests(), function (Request $request) use ($request_id) {
      return $request->attributes->get(static::SUBREQUEST_ID) == $request_id;
    });
    if (count($found)) {
      return reset($found);
    }
    // If the request is not in this level, then traverse the children's trees.
    $found = array_filter($this->getRequests(), function (Request $request) use ($request_id) {
      /** @var static $sub_tree */
      if (!$sub_tree = $request->attributes->get(static::SUBREQUEST_TREE)) {
        return FALSE;
      }

      return $sub_tree->getDescendant($request_id);
    });
    if (count($found)) {
      return reset($found);
    }

    return NULL;
  }

  /**
   * Is the request tree done?
   *
   * @return bool
   *   TRUE if all the requests in the tree and it's descendants are done.
   */
  public function isDone() {
    // The tree is done if all of the requests and their children are done.
    return array_reduce($this->getRequests(), function ($is_done, Request $request) {
      return $is_done && static::isRequestDone($request);
    }, TRUE);
  }

  /**
   * Resolves the JSON Pointer references.
   *
   * @todo For now we are forcing the use of JSON Pointer as the only format to
   * reference properties in existing responses. Allow pluggability, this step
   * should probably be better placed in the subrequest normalizer.
   *
   * @param \Symfony\Component\HttpFoundation\Response[] $responses
   *   Previous responses available.
   */
  public function dereference(array $responses) {
    $this->requests = array_map(function (Request $request) use ($responses) {
      $id = $request->attributes->get(static::SUBREQUEST_ID);
      $parent_id = $request->attributes->get(static::SUBREQUEST_PARENT_ID);
      // Allow replacement tokens in:
      //   1. The body.
      //   2. The path.
      //   3. The query string values.
      $content = $request->getContent();
      $changes = static::replaceAllOccurrences($responses, $content);
      $uri = $request->getUri();
      $changes += static::replaceAllOccurrences($responses, $uri);
      foreach ($request->query as $key => $value) {
        $new_key = $key;
        $query_changes = static::replaceAllOccurrences($responses, $new_key);
        $query_changes += static::replaceAllOccurrences($responses, $value);
        if ($query_changes) {
          $request->query->remove($key);
          $request->query->set($new_key, $value);
        }
      }

      // If there is anything to update.
      if ($changes) {
        // We need to duplicate the request to force recomputing the internal
        // caches.
        $request = static::cloneRequest($request, $uri, $content, $id, $parent_id);
      }

      return $request;
    }, $this->getRequests());
  }

  /**
   * Check if a request and all its possible children are done.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return bool
   *   TRUE if is done. FALSE otherwise.
   */
  protected static function isRequestDone(Request $request) {
    // If one request is not done, the whole tree is not done.
    if (!$request->attributes->get(static::SUBREQUEST_DONE)) {
      return FALSE;
    }
    // If the request has children, then make sure those are done too.
    /** @var static $sub_tree */
    if ($sub_tree = $request->attributes->get(static::SUBREQUEST_TREE)) {
      if (!$sub_tree->isDone()) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Do in-place replacements for an input string containing replacement tokens.
   *
   * @param array $responses
   *   The pool of responses where to find the replacement data.
   * @param string $input
   *   The string containing the token to replace. It's passed by reference and
   *   modified if necessary.
   *
   * @return int
   *   The number of replacements made.
   */
  public static function replaceAllOccurrences(array $responses, &$input) {
    if (is_array($input)) {
      $changes = 0;
      // Apply the replacement recursively on the array keys and values.
      foreach ($input as $key => $value) {
        $new_key = $key;
        $local_changes = static::replaceAllOccurrences($responses, $new_key);
        $local_changes += static::replaceAllOccurrences($responses, $value);
        $changes += $local_changes;
        if ($local_changes) {
          unset($input[$key]);
          $input[$new_key] = $value;
        }
      }
      return $changes;
    }
    // Detect {{/foo#/bar}}
    $pattern = '/\{\{\/([^\{\}]+)@(\/[^\{\}]+)\}\}/';
    $matches = [];
    if (!preg_match_all($pattern, $input, $matches)) {
      return 0;
    }
    for ($index = 0; $index < count($matches[1]); $index++) {
      $replacement = static::findReplacement(
        $responses,
        $matches[1][$index],
        $matches[2][$index]
      );
      $pattern = sprintf('/%s/', preg_quote($matches[0][$index], '/'));
      $input = preg_replace($pattern, $replacement, $input);
    }
    return $index;
  }

  /**
   * Find a replacement in the responses for the JSON pointer.
   *
   * @param \Symfony\Component\HttpFoundation\Response[] $responses
   *   The array of responses to look data into.
   * @param string $id
   *   The response ID to extract data from.
   * @param $json_pointer_path
   *   The JSON pointer path of the data to extract.
   *
   * @throws \Rs\Json\Pointer\NonexistentValueReferencedException
   *   When the referenced response was not found.
   *
   * @return mixed
   *   The contents of the pointed JSON property.
   */
  protected static function findReplacement(array $responses, $id, $json_pointer_path) {
    $found = array_filter($responses, function (Response $response) use ($id) {
      return $response->headers->get('Content-ID') === sprintf('<%s>', $id);
    });
    $response = reset($found);
    if (!$response instanceof Response) {
      throw new NonexistentValueReferencedException('Response is still not ready.');
    }
    // Find the data in the response output.
    $pointer = new Pointer($response->getContent());

    return $pointer->get($json_pointer_path);
  }

  /**
   * Clones a request and modifies certain parameters.
   *
   * We need to do this to reset some of the internal request caches. There may
   * be a better way of doing this, but I could not find it in the time that I
   * expected.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The original request.
   * @param $uri
   *   The (potentially) new URI.
   * @param $content
   *   The (potentially) new body content.
   * @param $id
   *   The subrequest id.
   * @param $parent_id
   *   The subrequest id of the parent.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   The cloned request.
   */
  protected static function cloneRequest(Request $request, $uri, $content, $id, $parent_id) {
    $request->server->set('REQUEST_URI', $uri);
    $sub_tree = $request->attributes->get(static::SUBREQUEST_TREE);
    $session = $request->getSession();
    $new_request = Request::create(
      $uri,
      $request->getMethod(),
      (array) $request->query->getIterator(),
      (array) $request->cookies->getIterator(),
      (array) $request->files->getIterator(),
      (array) $request->server->getIterator(),
      $content
    );
    // Set the sub-request headers.
    foreach ($request->headers as $key => $val) {
      $new_request->headers->set($key, $val);
    }
    $new_request->headers->set('Content-ID', sprintf('<%s>', $id));
    $new_request->attributes->set(static::SUBREQUEST_PARENT_ID, $parent_id);
    $new_request->attributes->set(static::SUBREQUEST_ID, $id);
    $new_request->attributes->set(static::SUBREQUEST_TREE, $sub_tree);
    $new_request->setSession($session);

    return $new_request;
  }

}
