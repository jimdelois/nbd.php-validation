<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;

/**
 * Validates that data is a haystack array that contains a certain needle
 */
class ContainedInRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      $haystack = $this->_extractContextParameters( $context );

      if ( is_array( $data ) || is_object( $data ) ) {
        return false;
      }

      return in_array( $data, $haystack );

    } );

    $this->setClosure( $closure );

  } // __construct

} // ContainedInRule
