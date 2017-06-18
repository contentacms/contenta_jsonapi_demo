<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Materialize;
use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Pre-processes variables for the "menu_local_action" theme hook.
 * todo: need this at all?
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("menu_local_action")
 */
class MenuLocalAction extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    $link = $element->getProperty('link');
    $link += ['localized_options' => []];
    $link['localized_options']['set_active_class'] = TRUE;

    $icon = Materialize::glyphiconFromString($link['title']);
    $options = isset($link['localized_options']) ? $link['localized_options'] : [];

    if (isset($link['url'])) {
      // Turn link into a mini-button and colorize based on title.
      $class = Materialize::cssClassFromString($link['title'], 'default');
      if (!isset($options['attributes']['class'])) {
        $options['attributes']['class'] = [];
      }
      $string = is_string($options['attributes']['class']);
      if ($string) {
        $options['attributes']['class'] = explode(' ', $options['attributes']['class']);
      }
      $options['attributes']['class'][] = 'btn';
      $options['attributes']['class'][] = 'btn-xs';
      $options['attributes']['class'][] = 'btn-' . $class;
      if ($string) {
        $options['attributes']['class'] = implode(' ', $options['attributes']['class']);
      }

      $variables['link'] = [
        '#type' => 'link',
        '#title' => $icon ? new FormattableMarkup(Element::create($icon)->renderPlain() . '@text', ['@text' => $link['title']]) : $link['title'],
        '#options' => $options,
        '#url' => $link['url'],
      ];
    }
    else {
      $variables['link'] = [
        '#type' => 'link',
        '#title' => $link['title'],
        '#options' => $options,
        '#url' => $link['url'],
      ];
    }
  }

}
