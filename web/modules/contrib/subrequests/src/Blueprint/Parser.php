<?php


namespace Drupal\subrequests\Blueprint;

use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Cache\CacheableResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * TODO: Change this comment. We'll use the serializer instead.
 * Base class for blueprint parsers. There may be slightly different blueprint
 * formats depending on the encoding. For instance, JSON encoded blueprints will
 * reference other properties in the responses using JSON pointers, while XML
 * encoded blueprints will use XPath.
 */
class Parser {

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * The Mime-Type of the incoming requests.
   *
   * @var string
   */
  protected $type;

  /**
   * Parser constructor.
   */
  public function __construct(SerializerInterface $serializer) {
    $this->serializer = $serializer;
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The master request to parse. We need from it:
   *     - Request body content.
   *     - Request mime-type.
   */
  public function parseRequest(Request $request) {
    $data = '';
    if ($request->getMethod() === Request::METHOD_POST) {
      $data = $request->getContent();
    }
    else if ($request->getMethod() === Request::METHOD_GET) {
      $data = $request->query->get('query', '');
    }
    $tree = $this->serializer->deserialize(
      $data,
      RequestTree::class,
      $request->getRequestFormat(),
      ['master_request' => $request]
    );
    $request->attributes->set(RequestTree::SUBREQUEST_TREE, $tree);
    // It assumed that all subrequests use the same Mime-Type.
    $this->type = $request->getMimeType($request->getRequestFormat());
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Response[] $responses
   *   The responses to combine.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The combined response with a 207.
   */
  public function combineResponses(array $responses) {
    $delimiter = md5(microtime());

    // Prepare the root content type header.
    $content_type = sprintf(
      'multipart/related; boundary="%s", type=%s',
      $delimiter,
      $this->type
    );
    $headers = ['Content-Type' => $content_type];

    $context = ['delimiter' => $delimiter];
    // Set the content.
    $content = $this->serializer->normalize($responses, 'multipart-related', $context);
    $response = CacheableResponse::create($content, 207, $headers);
    // Set the cacheability metadata.
    $cacheable_responses = array_filter($responses, function ($response) {
      return $response instanceof CacheableResponseInterface;
    });
    array_walk($cacheable_responses, function (CacheableResponseInterface $partial_response) use ($response) {
      $response->addCacheableDependency($partial_response->getCacheableMetadata());
    });

    return $response;
  }

  /**
   * Validates if a request can be constituted from this payload.
   *
   * @param array $data
   *   The user data representing a sub-request.
   *
   * @return bool
   *   TRUE if the data is valid. FALSE otherwise.
   */
  public static function isValidSubrequest(array $data) {
    // TODO: Implement this!
    return (bool) $data;
  }

}
