<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Interfaces\ValidatorServiceInterface;
use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;

/**
 * Ensures that two pieces of data are non-null and matching
 */
class MatchesRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  /**
   * {@inheritDoc}
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( empty( $context['validator'] ) || !( $context['validator'] instanceof ValidatorServiceInterface ) ) {
        throw new RuleRequirementException( "Validator required as context for '" . __CLASS__ . "'" );
      }

      list( $other_field_key ) = $this->_extractContextParameters( $context );

      $other_field = $context['validator']->getCageDataValue( $other_field_key );

      if ( $data === null || $other_field === null ) {
        return false;
      }

      return ( $data === $other_field );

    } );

    $this->setClosure( $closure );

  } // __construct

} // MatchesRule
