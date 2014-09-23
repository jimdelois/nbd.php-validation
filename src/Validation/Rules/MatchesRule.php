<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\CallbackRuleAbstract;

/**
 * Ensures that two pieces of data are non-null and matching
 */
class MatchesRule extends CallbackRuleAbstract {

  /**
   * {@inheritDoc}
   */
  public function __construct() {

    $closure = ( function( $field1, $field2 ) {

      if ( $field1 === null || $field2 === null ) {
        return false;
      }

      return ( $field1 === $field2 );

    } );

    $this->setClosure( $closure );

  } // __construct

} // MatchesRule
