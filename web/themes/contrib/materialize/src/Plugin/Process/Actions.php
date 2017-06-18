<?php

namespace Drupal\materialize\Plugin\Process;

use Drupal\materialize\Annotation\MaterializeProcess;
use Drupal\materialize\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Processes the "actions" element.
 *
 * @ingroup plugins_process
 *
 * @MaterializeProcess("actions")
 */
class Actions extends ProcessBase implements ProcessInterface {

  /**
   * {@inheritdoc}
   */
  public static function processElement(Element $element, FormStateInterface $form_state, array &$complete_form) {
    foreach ($element->children() as $child) {
      if ($child->isPropertyEmpty('icon')) {
        $child->setIcon();
      }
    }
  }

}
