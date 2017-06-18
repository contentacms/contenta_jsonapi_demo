<?php

namespace Drupal\materialize\Plugin\Setting\Components\Breadcrumbs;

use Drupal\materialize\Annotation\MaterializeSetting;
use Drupal\materialize\Plugin\Setting\SettingBase;
use Drupal\materialize\Utility\Element;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;

/**
 * The "breadcrumb_home" theme setting.
 *
 * @ingroup plugins_setting
 *
 * @MaterializeSetting(
 *   id = "breadcrumb_home",
 *   type = "checkbox",
 *   title = @Translation("Show 'Home' breadcrumb link"),
 *   description = @Translation("If your site has a module dedicated to handling breadcrumbs already, ensure this setting is enabled."),
 *   defaultValue = 0,
 *   groups = {
 *     "components" = @Translation("Components"),
 *     "breadcrumbs" = @Translation("Breadcrumbs"),
 *   },
 * )
 */
class BreadcrumbHome extends SettingBase {

  /**
   * {@inheritdoc}
   */
  public function alterFormElement(Element $form, FormStateInterface $form_state, $form_id = NULL) {
    $setting = $this->getSettingElement($form, $form_state);
    $setting->setProperty('states', [
      'invisible' => [
        ':input[name="breadcrumb"]' => ['value' => 0],
      ],
    ]);
  }

}
