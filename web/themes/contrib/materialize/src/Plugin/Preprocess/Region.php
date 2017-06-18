<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Materialize;
use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "region" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("region")
 */
class Region extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $region = $variables['elements']['#region'];
    $variables['region'] = $region;
    $variables['content'] = $variables['elements']['#children'];

    // Help region.
    if ($region === 'help' && !empty($variables['content'])) {
     
      /* todo: 
      $variables['content'] = [
        'icon' => Materialize::glyphicon('question-sign'),
        'content' => ['#markup' => $variables['content']],
      ]; */
    }

    // Support for "well" classes in regions.
    // static $region_wells;
    // if (!isset($region_wells)) {
    //   $region_wells = $this->theme->getSetting('region_wells');
    // }
    // if (!empty($region_wells[$region])) {
    //   $variables->addClass($region_wells[$region]);
    // }
  }

}
