<?php

namespace Drupal\materialize\Plugin\Setting\General\Tables;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "table_bordered" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "table_bordered",
 *   type = "checkbox",
 *   title = @Translation("Bordered table"),
 *   description = @Translation("Add borders on all sides of the table and cells."),
 *   defaultValue = 0,
 *   groups = {
 *     "general" = @Translation("General"),
 *     "tables" = @Translation("Tables"),
 *   },
 * )
 */
class TableBordered extends SettingBase {}
