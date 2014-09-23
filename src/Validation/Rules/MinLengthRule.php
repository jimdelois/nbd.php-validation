<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\CallbackRuleAbstract;
use NBD\Validation\Exceptions\Validator\RuleRequirementException;
use NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Validates that data is a string over a certain length of characters
 */
class MinLengthRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( empty( $context['parameters'] ) || count( $context['parameters'] ) !== 1 ) {
        throw new RuleRequirementException( "'" . __CLASS__ . "' requires one parameter" );
      }

      if ( !is_string( $data ) ) {
        return false;
      }

      $length = mb_strlen( $data, 'UTF-8' );

      list( $min_length ) = $context['parameters'];

      // Easily ensure this is a positive integer
      if ( !ctype_digit( (string)$min_length ) ) {
        throw new InvalidRuleException( "Minimum length must be a positive integer" );
      }

      return ( $length >= $min_length );

    } );

    $this->setClosure( $closure );

  } // __construct

} // MinLengthRule
