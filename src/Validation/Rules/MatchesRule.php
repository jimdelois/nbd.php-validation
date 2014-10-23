<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Interfaces\ValidatorServiceInterface;
use NBD\Validation\Abstracts\CallbackRuleAbstract;
use NBD\Validation\Exceptions\Validator\RuleRequirementException;

/**
 * Ensures that two pieces of data are non-null and matching
 */
class MatchesRule extends CallbackRuleAbstract {

  /**
   * {@inheritDoc}
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( empty( $context['validator'] ) || !$context['validator'] instanceof ValidatorServiceInterface ) {
        throw new RuleRequirementException( "Validator required as context for '" . __CLASS__ . "'" );
      }

      list( $other_field_key ) = $context['parameters'];

      $other_field = $context['validator']->getCageDataValue( $other_field_key );

      if ( $data === null || $other_field === null ) {
        return false;
      }

      return ( $data === $other_field );

    } );

    $this->setClosure( $closure );

  } // __construct

} // MatchesRule
