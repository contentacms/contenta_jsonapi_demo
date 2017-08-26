<?php


namespace Drupal\subrequests\Normalizer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MultiresponseNormalizer implements NormalizerInterface {

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $delimiter = $context['delimiter'];
    $separator = sprintf("\r\n--%s\r\n", $delimiter);
    // Join the content responses with the separator.
    $content_items = array_map(function (Response $part_response) {
      $part_response->headers->set('Status', $part_response->getStatusCode());
      return sprintf(
        "%s\r\n%s",
        $part_response->headers,
        $part_response->getContent()
      );
    }, (array) $object);
    return sprintf("--%s\r\n", $delimiter) . implode($separator, $content_items) . sprintf("\r\n--%s--", $delimiter);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    if ($format !== 'multipart-related') {
      return FALSE;
    }
    if (!is_array($data)) {
      return FALSE;
    }
    $responses = array_filter($data, function ($response) {
      return $response instanceof Response;
    });
    if (count($responses) !== count($data)) {
      return FALSE;
    }
    return TRUE;
  }

}
