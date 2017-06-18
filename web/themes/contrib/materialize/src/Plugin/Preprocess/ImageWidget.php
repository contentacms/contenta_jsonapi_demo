<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "image_widget" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @see image-widget.html.twig
 *
 * @MaterializePreprocess("image_widget",
 *   replace = "template_preprocess_image_widget"
 * )
 */
class ImageWidget extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    $variables->addClass(['image-widget', 'js-form-managed-file', 'form-managed-file', 'clearfix']);

    $data = &$variables->offsetGet('data', []);
    foreach ($element->children() as $key => $child) {
      $data[$key] = $child->getArray();
    }
  }

}
