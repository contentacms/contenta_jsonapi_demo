<?php

namespace Drupal\materialize\Plugin\Setting\General\Tables;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "table_hover" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "table_hover",
 *   type = "checkbox",
 *   title = @Translation("Hover rows"),
 *   description = @Translation("Enable a hover state on table rows."),
 *   defaultValue = 1,
 *   groups = {
 *     "general" = @Translation("General"),
 *     "tables" = @Translation("Tables"),
 *   },
 * )
 */
class TableHover extends SettingBase {}
