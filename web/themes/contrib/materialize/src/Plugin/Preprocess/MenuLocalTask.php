<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Materialize;
use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Pre-processes variables for the "menu_local_action" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("menu_local_task")
 */
class MenuLocalTask extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    $link = $element->getProperty('link');
    $link += ['localized_options' => []];
    $link['localized_options']['set_active_class'] = TRUE;

    $icon = Materialize::glyphiconFromString($link['title']);
    $options = isset($link['localized_options']) ? $link['localized_options'] : [];

    $options['attributes']['target'] = '_self';

    $variables['link'] = [
      '#type' => 'link',
      '#title' => $link['title'],
      '#options' => $options,
      '#url' => $link['url'],
    ];
  }

}
