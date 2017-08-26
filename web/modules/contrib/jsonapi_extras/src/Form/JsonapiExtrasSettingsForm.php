<?php

namespace Drupal\jsonapi_extras\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure JSON API settings for this site.
 */
class JsonapiExtrasSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['jsonapi_extras.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'jsonapi_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('jsonapi_extras.settings');

    $form['path_prefix'] = [
      '#title' => $this->t('Path prefix'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#field_prefix' => '/',
      '#description' => $this->t('The path prefix for JSON API.'),
      '#default_value' => $config->get('path_prefix'),
    ];

    $form['include_count'] = [
      '#title' => $this->t('Include count in collection queries'),
      '#type' => 'checkbox',
      '#description' => $this->t('If activated, all collection responses will return a total record count for the provided query.'),
      '#default_value' => $config->get('include_count'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($path_prefix = $form_state->getValue('path_prefix')) {
      $this->config('jsonapi_extras.settings')
        ->set('path_prefix', trim($path_prefix, '/'))
        ->save();
    }

    $this->config('jsonapi_extras.settings')
      ->set('include_count', $form_state->getValue('include_count'))
      ->save();

    // Rebuild the router.
    \Drupal::service('router.builder')->setRebuildNeeded();

    parent::submitForm($form, $form_state);
  }

}
