<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "input" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("input")
 */
class Input extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    $element->map(['id', 'name', 'value', 'type']);

    // Autocomplete.
    if ($route = $element->getProperty('autocomplete_route_name')) {
      $variables['autocomplete'] = TRUE;
    }

    // Create variables for #input_group and #input_group_button flags.
    $variables['input_group'] = $element->getProperty('input_group') || $element->getProperty('input_group_button');

    // Map the element properties.
    $variables->map([
      'attributes' => 'attributes',
      'icon' => 'icon',
      'field_prefix' => 'prefix',
      'field_suffix' => 'suffix',
      'type' => 'type',
    ]);

    // Ensure attributes are proper objects.
    $this->preprocessAttributes();
  }

}
