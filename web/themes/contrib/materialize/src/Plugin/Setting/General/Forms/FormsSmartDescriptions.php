<?php

namespace Drupal\materialize\Plugin\Setting\General\Forms;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "forms_smart_descriptions" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "forms_smart_descriptions",
 *   type = "checkbox",
 *   title = @Translation("Smart form descriptions (via Tooltips)"),
 *   defaultValue = 1,
 *   description = @Translation("Convert descriptions into tooltips (must be enabled) automatically based on certain criteria. This helps reduce the, sometimes unnecessary, amount of noise on a page full of form elements."),
 *   groups = {
 *     "general" = @Translation("General"),
 *     "forms" = @Translation("Forms"),
 *   },
 * )
 */
class FormsSmartDescriptions extends SettingBase {}
