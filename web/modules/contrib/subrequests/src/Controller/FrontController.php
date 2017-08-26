<?php


namespace Drupal\subrequests\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\subrequests\Blueprint\Parser;
use Drupal\subrequests\Blueprint\RequestTree;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FrontController extends ControllerBase {

  /**
   * @var \Drupal\subrequests\Blueprint\Parser
   */
  protected $parser;

  /**
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * FrontController constructor.
   */
  public function __construct(Parser $parser, HttpKernelInterface $http_kernel) {
    $this->parser = $parser;
    $this->httpKernel = $http_kernel;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('subrequests.blueprint_parser'),
      $container->get('http_kernel')
    );
  }

  /**
   * Controller handler.
   */
  public function handle(Request $request) {
    $this->parser->parseRequest($request);
    $responses = [];
    /** @var \Drupal\subrequests\Blueprint\RequestTree $tree */
    $root_tree = $request->attributes->get(RequestTree::SUBREQUEST_TREE);
    $trees = [$root_tree];
    // Handle all the sub-requests.
    while (!$root_tree->isDone()) {
      // Requests in the current level may have references to older responses.
      // This step resolves those.
      array_walk($trees, function (RequestTree $tree) use ($responses) {
        $tree->dereference($responses);
      });
      // Get all the requests in the trees for the previous pass.
      $requests = array_reduce($trees, function (array $carry, RequestTree $tree) {
        return array_merge($carry, $tree->getRequests());
      }, []);
      // Get the next batch of trees for the next level.
      $trees = array_reduce($trees, function (array $carry, RequestTree $tree) {
        return array_merge($carry, $tree->getSubTrees());
      }, []);
      // Handle the requests for the trees at this level and gather the
      // responses.
      $level_responses = array_map(function (Request $request) {
        $response = $this->httpKernel->handle($request, HttpKernelInterface::MASTER_REQUEST);
        // Manually mark the request as done. We cannot use a response
        // subscriber, since it may not fire if the subrequest is cached by
        // PageCache.
        $request->attributes->set(RequestTree::SUBREQUEST_DONE, TRUE);
        $id = $request->headers->get('Content-ID');
        $response->headers->set('Content-ID', $id);

        return $response;
      }, $requests);
      $responses = array_merge(
        $responses,
        $level_responses
      );
    }

    return $this->parser->combineResponses($responses);
  }

}
