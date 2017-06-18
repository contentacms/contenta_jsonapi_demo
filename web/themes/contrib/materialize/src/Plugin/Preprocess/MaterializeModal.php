<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Annotation\MaterializePreprocess;
use Drupal\materialize\Utility\Variables;
use Drupal\Component\Utility\Html;

/**
 * Pre-processes variables for the "materialize_modal" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("materialize_modal")
 */
class MaterializeModal extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  protected function preprocessVariables(Variables $variables) {
    // Immediately log an error and return if Materialize modals are not enabled.
    if (!$this->theme->getSetting('modal_enabled')) {
      \Drupal::logger('materialize')->error(t('Materialize modals are not enabled.'));
      return;
    }

    // Retrieve the ID, generating one if needed.
    $id = $variables->getAttribute('id', Html::getUniqueId($variables->offsetGet('id', 'materialize-modal')));
    $variables->setAttribute('id', $id);
    unset($variables['id']);

    if ($variables->title) {
      $title_id = $variables->getAttribute('id', "$id--title", $variables::TITLE);
      $variables->setAttribute('id', $title_id, $variables::TITLE);
      $variables->setAttribute('aria-labelledby', $title_id);
    }

    // Use a provided modal size or retrieve the default theme setting.
    $variables->size = $variables->size ?: $this->theme->getSetting('modal_size');

    // Convert the description variable.
    $this->preprocessDescription();

    // Ensure all attributes are proper objects.
    $this->preprocessAttributes();
  }

}
