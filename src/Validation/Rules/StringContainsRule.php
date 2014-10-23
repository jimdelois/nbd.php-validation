<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Validates that data is a string that contains a sequence of characters
 */
class StringContainsRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $needle, array $context ) {

      if ( !isset( $context['parameters'] ) || count( $context['parameters'] ) !== 1 ) {
        throw new RuleRequirementException( "One parameter required for '" . __CLASS__ . "'" );
      }

      if ( !is_string( $needle ) ) {
        return false;
      }

      list( $haystack ) = $context['parameters'];

      if ( !is_string( $haystack ) ) {
        throw new InvalidRuleException( "Context parameter must be a string" );
      }

      return ( mb_strpos( $haystack, $needle, 0, 'UTF-8' ) !== false );

    } );

    $this->setClosure( $closure );

  } // __construct

} // StringContainsRule
