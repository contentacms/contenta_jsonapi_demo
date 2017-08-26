<?php


namespace Drupal\subrequests\Normalizer;

use Drupal\subrequests\Blueprint\RequestTree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;

class JsonBlueprintDenormalizer implements DenormalizerInterface, SerializerAwareInterface {

  /**
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * {@inheritdoc}
   */
  public function setSerializer(SerializerInterface $serializer) {
    if (!is_a($serializer, Serializer::class)) {
      throw new \ErrorException('Serializer is unable to normalize or denormalize.');
    }
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public function denormalize($data, $class, $format = NULL, array $context = []) {
    // The top level is an array of normalized requests.
    $requests = array_map(function ($item) use ($format, $context) {
      return $this->serializer->denormalize($item, Request::class, $format, $context);
    }, $data);
    // We want to create one tree per parent, but for that we need to identify
    // the parents first.
    $requests_per_parent = array_reduce($requests, function (array $carry, Request $request) {
      $parent_id = $request->attributes
        ->get(RequestTree::SUBREQUEST_PARENT_ID, RequestTree::ROOT_TREE_ID);
      if (empty($carry[$parent_id])) {
        $carry[$parent_id] = [];
      }
      $carry[$parent_id][] = $request;
      return $carry;
    }, []);
    // Now get all the requests for the root parent to create the root tree.
    $root_tree = new RequestTree($requests_per_parent[RequestTree::ROOT_TREE_ID]);
    unset($requests_per_parent[RequestTree::ROOT_TREE_ID]);

    // Iterate through all the parents to find them in the tree. The attach the
    // sub-tree to the root.
    // TODO: If a tree hangs from a parent that is not attached to the root, then this process may fail.
    foreach ($requests_per_parent as $parent_id => $children_requests) {
      $parent_requests = array_filter($requests, function (Request $request) use ($parent_id) {
        return $request->attributes->get(RequestTree::SUBREQUEST_ID) == $parent_id;
      });
      $parent_request = reset($parent_requests);
      $parent_request->attributes->set(
        RequestTree::SUBREQUEST_TREE,
        new RequestTree($children_requests, $parent_id)
      );
    }

    return $root_tree;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDenormalization($data, $type, $format = NULL) {
    return $format === 'json'
      && $type === RequestTree::class
      && is_array($data)
      && !static::arrayIsKeyed($data);
  }

  /**
   * Check if an array is keyed.
   *
   * @param array $input
   *   The input array to check.
   *
   * @return bool
   *   True if the array is keyed.
   */
  public static function arrayIsKeyed(array $input) {
    $keys = array_keys($input);
    // If the array does not start at 0, it is not numeric.
    if ($keys[0] !== 0) {
      return TRUE;
    }
    // If there is a non-numeric key, the array is not numeric.
    $numeric_keys = array_filter($keys, 'is_numeric');
    if (count($keys) != count($numeric_keys)) {
      return TRUE;
    }
    // If the keys are not following the natural numbers sequence, then it is
    // not numeric.
    for ($index = 1; $index < count($keys); $index++) {
      if ($keys[$index] - $keys[$index - 1] !== 1) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
