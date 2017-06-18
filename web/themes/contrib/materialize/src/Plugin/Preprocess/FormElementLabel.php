<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "form_element_label" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("form_element_label")
 */
class FormElementLabel extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    // Map the element properties.
    $variables->map(['attributes', 'is_checkbox', 'is_radio']);

    // Preprocess attributes.
    $this->preprocessAttributes();
  }

}
