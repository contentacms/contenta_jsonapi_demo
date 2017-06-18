<?php

namespace Drupal\materialize\Plugin\Prerender;

use Drupal\materialize\Annotation\MaterializePrerender;

/**
 * Pre-render callback for the "operations" element type.
 *
 * @ingroup plugins_prerender
 *
 * @MaterializePrerender("operations",
 *   replace = "Drupal\Core\Render\Element\Operations::preRenderDropbutton"
 * )
 *
 * @see \Drupal\materialize\Plugin\Prerender\Dropbutton
 * @see \Drupal\Core\Render\Element\Operations::preRenderDropbutton()
 */
class Operations extends Dropbutton {}
