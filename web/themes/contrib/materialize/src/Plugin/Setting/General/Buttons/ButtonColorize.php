<?php

namespace Drupal\materialize\Plugin\Setting\General\Buttons;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "button_colorize" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "button_colorize",
 *   type = "checkbox",
 *   title = @Translation("Colorize Buttons"),
 *   defaultValue = 1,
 *   description = @Translation("Adds classes to buttons based on their text value."),
 *   groups = {
 *     "general" = @Translation("General"),
 *     "button" = @Translation("Buttons"),
 *   },
 *   see = {
 *     "http://getmaterialize.com/css/#buttons" = @Translation("Buttons"),
 *     "http://drupal-materialize.org/apis/hook_materialize_colorize_text_alter" = @Translation("hook_materialize_colorize_text_alter()"),
 *   },
 * )
 */
class ButtonColorize extends SettingBase {}
