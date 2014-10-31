<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Validates that data is a string that contains a sequence of characters
 */
class StringContainsRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $needle, array $context ) {

      if ( !is_string( $needle ) ) {
        return false;
      }

      list( $haystack ) = $this->_extractContextParameters( $context );

      if ( !is_string( $haystack ) ) {
        throw new InvalidRuleException( "Context parameter must be a string" );
      }

      return ( mb_strpos( $haystack, $needle, 0, 'UTF-8' ) !== false );

    } );

    $this->setClosure( $closure );

  } // __construct

} // StringContainsRule
