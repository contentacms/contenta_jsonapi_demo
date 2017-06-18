<?php

namespace Drupal\materialize\Plugin\Setting\General\Forms;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "forms_has_error_value_toggle" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "forms_has_error_value_toggle",
 *   type = "checkbox",
 *   title = @Translation("Automatically remove error classes when values have been entered"),
 *   description = @Translation("If an element has a <code>.has-error</code> class attached to it, enabling this will automatically remove that class when a value is entered."),
 *   defaultValue = 1,
 *   groups = {
 *     "general" = @Translation("General"),
 *     "forms" = @Translation("Forms"),
 *   },
 * )
 */
class FormsHasErrorValueToggle extends SettingBase {

  /**
   * {@inheritdoc}
   */
  public function drupalSettings() {
    return TRUE;
  }

}
