<?php

namespace Drupal\materialize\Plugin\Setting\General\Buttons;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "button_size" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "button_size",
 *   type = "select",
 *   title = @Translation("Default button size"),
 *   defaultValue = "",
 *   description = @Translation("Defines the Materialize Buttons specific size"),
 *   empty_option = @Translation("Normal"),
 *   groups = {
 *     "general" = @Translation("General"),
 *     "button" = @Translation("Buttons"),
 *   },
 *   options = {
 *     "btn-xs" = @Translation("Extra Small"),
 *     "btn-sm" = @Translation("Small"),
 *     "btn-lg" = @Translation("Large"),
 *   },
 * )
 */
class ButtonSize extends SettingBase {}
