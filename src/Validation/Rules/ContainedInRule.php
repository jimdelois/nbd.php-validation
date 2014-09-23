<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\CallbackRuleAbstract;
use NBD\Validation\Exceptions\Validator\RuleRequirementException;

/**
 * Validates that data is a haystack array that contains a certain needle
 */
class ContainedInRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( empty( $context['parameters'] ) || !is_array( $context['parameters'] ) ) {
        throw new RuleRequirementException( "Parameters required for '" . __CLASS__ . "'" );
      }

      if ( is_array( $data ) || is_object( $data ) ) {
        return false;
      }

      return in_array( $data, $context['parameters'] );

    } );

    $this->setClosure( $closure );

  } // __construct

} // ContainedInRule
