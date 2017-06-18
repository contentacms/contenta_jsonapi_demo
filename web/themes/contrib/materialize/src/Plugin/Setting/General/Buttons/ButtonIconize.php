<?php

namespace Drupal\materialize\Plugin\Setting\General\Buttons;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "button_iconize" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "button_iconize",
 *   type = "checkbox",
 *   title = @Translation("Iconize Buttons"),
 *   defaultValue = 1,
 *   description = @Translation("Adds icons to buttons based on the text value"),
 *   groups = {
 *     "general" = @Translation("General"),
 *     "button" = @Translation("Buttons"),
 *   },
 *   see = {
 *     "http://drupal-materialize.org/apis/hook_materialize_iconize_text_alter" = @Translation("hook_materialize_iconize_text_alter()"),
 *   },
 * )
 */
class ButtonIconize extends SettingBase {}
