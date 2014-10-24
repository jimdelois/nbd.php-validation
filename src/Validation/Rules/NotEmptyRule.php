<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;

class NotEmptyRule extends CallbackRuleAbstract {

  /**
   * {@inheritDoc}
   */
  public function __construct() {

    $closure = ( function( $data ) {
      return !empty( $data );
    } );

    $this->setClosure( $closure );

  } // __construct

} // NotEmptyRule
