<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Utility\Variables;
use Drupal\Core\Template\Attribute;

/**
 * Pre-processes variables for the "pager" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("pager")
 */
class Pager extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    foreach ($variables['items'] as $name => $item) {
      if ('pages' == $name) {
        foreach ($item as $num => $page) {
          if (empty($page['attributes'])) {
            $variables['items'][$name][$num]['attributes'] = new Attribute(['class' => ['waves-effect']]);
          }
          else {
            $variables['items'][$name][$num]['attributes'].addClass('waves-effect');
          }
        }
      }
      elseif (empty($item['attributes'])) {
        $variables['items'][$name]['attributes'] = new Attribute(['class' => ['waves-effect']]);
      }
      else {
        $variables['items'][$name]['attributes'].addClass('waves-effect');
      }
    }
  }

}
