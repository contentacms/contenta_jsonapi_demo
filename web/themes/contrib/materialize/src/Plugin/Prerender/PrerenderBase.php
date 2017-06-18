<?php

namespace Drupal\materialize\Plugin\Prerender;

use Drupal\materialize\Utility\Element;

/**
 * Defines the interface for an object oriented preprocess plugin.
 *
 * @ingroup plugins_prerender
 */
class PrerenderBase implements PrerenderInterface {

  /**
   * {@inheritdoc}
   */
  public static function preRender(array $element) {
    static::preRenderElement(Element::create($element));
    return $element;
  }

  /**
   * Pre-render element callback.
   *
   * @param \Drupal\materialize\Utility\Element $element
   *   The element object.
   */
  public static function preRenderElement(Element $element) {}

}
