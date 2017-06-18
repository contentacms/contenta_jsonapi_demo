<?php

namespace Drupal\materialize\Plugin\Setting\Components\Breadcrumbs;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\Core\Annotation\Translation;

/**
 * The "breadcrumb" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "breadcrumb",
 *   type = "select",
 *   title = @Translation("Breadcrumb visibility"),
 *   description = @Translation("Show or hide the Breadcrumbs"),
 *   defaultValue = "1",
 *   groups = {
 *     "components" = @Translation("Components"),
 *     "breadcrumbs" = @Translation("Breadcrumbs"),
 *   },
 *   options = {
 *     0 = @Translation("Hidden"),
 *     1 = @Translation("Visible"),
 *     2 = @Translation("Only in admin areas"),
 *   },
 * )
 */
class Breadcrumb extends SettingBase {}
