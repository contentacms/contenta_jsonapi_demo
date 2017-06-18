<?php

namespace Drupal\jsonapi_extras\ResourceType;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\jsonapi\ResourceType\ResourceTypeRepository;
use Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerManager;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

/**
 * Provides a repository of JSON API configurable resource types.
 */
class ConfigurableResourceTypeRepository extends ResourceTypeRepository {

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Plugin manager for enhancers.
   *
   * @var \Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerManager
   */
  protected $enhancerManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $bundle_manager, EntityRepositoryInterface $entity_repository, ResourceFieldEnhancerManager $enhancer_manager) {
    parent::__construct($entity_type_manager, $bundle_manager);
    $this->entityRepository = $entity_repository;
    $this->enhancerManager = $enhancer_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function all() {
    if (!$this->all) {
      $this->all = $this->getResourceTypes(FALSE);
    }
    return $this->all;
  }

  /**
   * {@inheritdoc}
   */
  public function get($entity_type_id, $bundle) {
    if (empty($entity_type_id)) {
      throw new PreconditionFailedHttpException('Server error. The current route is malformed.');
    }

    foreach ($this->getResourceTypes() as $resource) {
      if ($resource->getEntityTypeId() == $entity_type_id && $resource->getBundle() == $bundle) {
        return $resource;
      }
    }

    return NULL;
  }

  /**
   * Returns an array of resource types.
   *
   * @param bool $include_disabled
   *   TRUE to included disabled resource types.
   *
   * @return array
   *   An array of resource types.
   */
  public function getResourceTypes($include_disabled = TRUE) {
    $entity_type_ids = array_keys($this->entityTypeManager->getDefinitions());

    $resource_types = [];
    foreach ($entity_type_ids as $entity_type_id) {
      $bundles = array_keys($this->bundleManager->getBundleInfo($entity_type_id));
      $current_types = array_map(function ($bundle) use ($entity_type_id, $include_disabled) {
        $resource_config_id = sprintf('%s--%s', $entity_type_id, $bundle);
        $resource_config = $this->entityRepository->loadEntityByConfigTarget(
          'jsonapi_resource_config',
          $resource_config_id
        );
        $resource_config = $resource_config ?: new NullJsonapiResourceConfig([], '');
        if (!$include_disabled && $resource_config->get('disabled')) {
          return NULL;
        }
        return new ConfigurableResourceType(
          $entity_type_id,
          $bundle,
          $this->entityTypeManager->getDefinition($entity_type_id)->getClass(),
          $resource_config,
          $this->enhancerManager
        );
      }, $bundles);
      $resource_types = array_merge($resource_types, $current_types);
    }

    return array_filter($resource_types);
  }

}
