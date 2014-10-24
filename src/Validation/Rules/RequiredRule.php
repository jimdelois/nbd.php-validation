<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;

class RequiredRule extends CallbackRuleAbstract {

  protected $_error_template = "%fieldname% is required";

  /**
   * {@inheritDoc}
   */
  public function __construct() {

    $closure = ( function( $data ) {
      return ( $data !== null );
    } );

    $this->setClosure( $closure );

  } // __construct

} // RequiredRule
