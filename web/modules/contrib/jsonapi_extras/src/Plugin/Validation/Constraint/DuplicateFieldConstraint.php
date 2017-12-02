<?php

namespace Drupal\jsonapi_extras\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Constraint(
 *   id = "jsonapi_extras__duplicate_field",
 *   label = @Translation("Duplicate field", context = "Validation")
 * )
 */
class DuplicateFieldConstraint extends Constraint {

  public $message = 'The override must be unique.';

}
