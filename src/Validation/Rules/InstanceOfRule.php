<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\CallbackRuleAbstract;
use NBD\Validation\Exceptions\Validator\RuleRequirementException;

/**
 * Validates that data is a string that contains a sequence of characters
 */
class InstanceOfRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( empty( $context['parameters'] ) || count( $context['parameters'] ) !== 1 ) {
        throw new RuleRequirementException( "One parameter is required for '" . __CLASS__ . "'" );
      }

      if ( !is_object( $data ) ) {
        return false;
      }

      list( $class_name ) = $context['parameters'];

      return ( $data instanceof $class_name );

    } );

    $this->setClosure( $closure );

  } // __construct

} // InstanceOfRule
