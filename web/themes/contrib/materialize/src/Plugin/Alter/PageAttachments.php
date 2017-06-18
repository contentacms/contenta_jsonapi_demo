<?php

namespace Drupal\materialize\Plugin\Alter;

use Drupal\materialize\Annotation\MaterializeAlter;
use Drupal\materialize\Plugin\PluginBase;

/**
 * Implements hook_page_attachments_alter().
 *
 * @ingroup plugins_alter
 *
 * @MaterializeAlter("page_attachments")
 */
class PageAttachments extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$attachments, &$context1 = NULL, &$context2 = NULL) {
    if ($this->theme->livereloadUrl()) {
      $attachments['#attached']['library'][] = 'materialize/livereload';
    }
    if ($this->theme->getSetting('popover_enabled')) {
      $attachments['#attached']['library'][] = 'materialize/popover';
    }
    if ($this->theme->getSetting('tooltip_enabled')) {
      $attachments['#attached']['library'][] = 'materialize/tooltip';
    }
    $attachments['#attached']['drupalSettings']['materialize'] = $this->theme->drupalSettings();
  }

}
