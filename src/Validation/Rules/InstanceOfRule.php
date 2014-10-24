<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;

/**
 * Validates that data is a string that contains a sequence of characters
 */
class InstanceOfRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( !is_object( $data ) ) {
        return false;
      }

      list( $class_name ) = $this->_extractContextParameters( $context );

      return ( $data instanceof $class_name );

    } );

    $this->setClosure( $closure );

  } // __construct

} // InstanceOfRule
