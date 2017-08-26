<?php

namespace Drupal\jsonapi_extras\Plugin\jsonapi\FieldEnhancer;

use Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerBase;

/**
 * Perform additional manipulations to date fields.
 *
 * @ResourceFieldEnhancer(
 *   id = "date_time",
 *   label = @Translation("Date Time"),
 *   description = @Translation("Formats a date based the configured date format.")
 * )
 */
class DateTimeEnhancer extends ResourceFieldEnhancerBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'dateTimeFormat' => \DateTime::ISO8601,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function postProcess($value) {
    $date = new \DateTime();
    $date->setTimestamp($value);
    $configuration = $this->getConfiguration();

    return $date->format($configuration['dateTimeFormat']);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareForInput($value) {
    $date = new \DateTime($value);

    return (int) $date->format('U');
  }

  /**
   * {@inheritdoc}
   */
  public function getJsonSchema() {
    return [
      'type' => 'string',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $resource_field_info) {
    $settings = empty($resource_field_info['enhancer']['settings'])
      ? $this->getConfiguration()
      : $resource_field_info['enhancer']['settings'];

    return [
      'dateTimeFormat' => [
        '#type' => 'textfield',
        '#title' => $this->t('Format'),
        '#description' => $this->t('Use a valid date format.'),
        '#default_value' => $settings['dateTimeFormat'],
      ],
    ];
  }

}
