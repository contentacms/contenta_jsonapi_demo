<?php

namespace Drupal\docson\Form;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements the ajax demo form controller.
 *
 * This example demonstrates using ajax callbacks to populate the options of a
 * color select element dynamically based on the value selected in another
 * select element in the form.
 *
 * @see \Drupal\Core\Form\FormBase
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class EntityBundlePicker extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $bundleInfo;

  /**
   * EntityBundlePicker constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundleInfo
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, EntityTypeBundleInfoInterface $bundleInfo) {
    $this->entityTypeManager = $entityTypeManager;
    $this->bundleInfo = $bundleInfo;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $content_entity_types = array_filter($this->entityTypeManager->getDefinitions(), function (EntityTypeInterface $entity_type) {
      return $entity_type instanceof ContentEntityTypeInterface;
    });
    $entity_types = array_reduce($content_entity_types, function ($carry, EntityTypeInterface $entity_type) {
      $carry[$entity_type->id()] = $entity_type->getLabel();
      return $carry;
    }, []);

    $form['data_format'] = [
      '#title' => $this->t('Schema Format'),
      '#description' => $this->t('The data may be represented in different formats. These are the formats supported by Schemata.'),
      '#type' => 'select',
      '#required' => TRUE,
      '#options' => [
        'api_json' => $this->t('JSON API'),
        'json' => $this->t('JSON'),
        'hal_json' => $this->t('HAL'),
      ]
    ];

    $form['entity_type_id'] = [
      '#title' => $this->t('Entity Type'),
      '#type' => 'select',
      '#options' => $entity_types,
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::bundleCallback',
        'wrapper' => 'bundle-wrapper',
      ],
    ];

    // Disable caching on this form.
    $form_state->setCached(FALSE);

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['bundle_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'bundle-wrapper'],
    ];
    if ($entity_type_id = $form_state->getValue('entity_type_id')) {
      $has_bundles = (bool) $this->entityTypeManager
        ->getDefinition($entity_type_id)->getBundleEntityType();
      if ($has_bundles) {
        $bundles = [];
        $bundle_info = $this->bundleInfo->getBundleInfo($entity_type_id);
        foreach ($bundle_info as $bundle_id => $info) {
          $bundles[$bundle_id] = $info['translatable']
            ? $this->t($info['label'])
            : $info['label'];
        }
        // Add a color element to the bundle_wrapper container with the bundles for
        // a given entity type.
        $form['bundle_wrapper']['bundle'] = [
          '#type' => 'select',
          '#empty_option' => $this->t('- Select -'),
          '#title' => $this->t('Bundle'),
          '#options' => $bundles,
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'docson_entity_bundle_picker';
  }

  /**
   * Implements callback for Ajax event on entity type selection.
   *
   * @param array $form
   *   From render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current state of form.
   *
   * @return array
   *   Color selection section of the form.
   */
  public function bundleCallback(array &$form, FormStateInterface $form_state) {
    return $form['bundle_wrapper'];
  }

  /**
   * Implements a form submit handler.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $parts = ['schemata', $form_state->getValue('entity_type_id')];
    if ($bundle = $form_state->getValue('bundle')) {
      $parts[] = $bundle;
    }
    $parts[] = sprintf(
      '?_format=%s&_describes=%s',
      'schema_json',
      $form_state->getValue('data_format')
    );
    $form_state->setRedirect('docson.schema_inspector', [], [
      'query' => ['schema' => '/' . implode('/', $parts)],
    ]);
  }

}
