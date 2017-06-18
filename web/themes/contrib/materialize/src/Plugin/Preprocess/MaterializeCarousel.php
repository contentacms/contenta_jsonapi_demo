<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Materialize;
use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Pre-processes variables for the "materialize_carousel" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("materialize_carousel")
 */
class MaterializeCarousel extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  protected function preprocessVariables(Variables $variables) {
    // Retrieve the ID, generating one if needed.
    $id = $variables->getAttribute('id', Html::getUniqueId($variables->offsetGet('id', 'materialize-carousel')));
    unset($variables['id']);

    // Build slides.
    foreach ($variables->slides as $key => &$slide) {
      if (!isset($slide['attributes'])) {
        $slide['attributes'] = [];
      }
      $slide['attributes'] = new Attribute($slide['attributes']);
    }

    // Build controls.
    if ($variables->controls) {
      $left_icon = Materialize::glyphicon('chevron-left');
      $right_icon = Materialize::glyphicon('chevron-right');
      $url = Url::fromUserInput("#$id");
      $variables->controls = [
        'left' => [
          '#type' => 'link',
          '#title' => new FormattableMarkup(Element::create($left_icon)->renderPlain() . '<span class="sr-only">@text</span>', ['@text' => t('Previous')]),
          '#url' => $url,
          '#attributes' => [
            'class' => ['left', 'carousel-control'],
            'role' => 'button',
            'data-slide' => 'prev',
          ],
        ],
        'right' => [
          '#type' => 'link',
          '#title' => new FormattableMarkup(Element::create($right_icon)->renderPlain() . '<span class="sr-only">@text</span>', ['@text' => t('Next')]),
          '#url' => $url,
          '#attributes' => [
            'class' => ['right', 'carousel-control'],
            'role' => 'button',
            'data-slide' => 'next',
          ],
        ],
      ];
    }

    // Build indicators.
    if ($variables->indicators) {
      $variables->indicators = [
        '#theme' => 'item_list__materialize_carousel_indicators',
        '#list_type' => 'ol',
        '#items' => array_keys($variables->slides),
        '#target' => "#$id",
        '#start_index' => $variables->start_index,
      ];
    }

    // Ensure all attributes are proper objects.
    $this->preprocessAttributes();
  }

}
