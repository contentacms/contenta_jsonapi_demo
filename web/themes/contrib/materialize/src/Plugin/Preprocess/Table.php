<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "table" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("table")
 */
class Table extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $responsive = (string) $this->theme->getSetting('table_responsive');
    switch ($responsive) {
      case '-1':
        $variables['responsive'] = !\Drupal::service('router.admin_context')->isAdminRoute();
        break;
      case '0':
        $variables['responsive'] = FALSE;
        break;
      case '1':
        $variables['responsive'] = TRUE;
        break;
    }
  }

}
