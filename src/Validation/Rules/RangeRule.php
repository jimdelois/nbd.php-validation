<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Validates that data is a string that contains a sequence of characters
 */
class RangeRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 2;

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( is_array( $data ) || is_object( $data ) ) {
        return false;
      }

      list( $min, $max ) = $this->_extractContextParameters( $context );

      // IMPORTANT: cast min/max to strings for is_numeric check, otherwise there will be mixed results
      if ( !is_numeric( (string)$min ) || !is_numeric( (string)$max ) ) {
        throw new InvalidRuleException( "Min and max parameters must be numeric values" );
      }

      if ( $min >= $max ) {
        throw new RuleRequirementException( "Range minimum ({$min}) must be less than maximum {$max})" );
      }

      return ( $data >= $min && $data <= $max );

    } );

    $this->setClosure( $closure );

  } // __construct

} // RangeRule
