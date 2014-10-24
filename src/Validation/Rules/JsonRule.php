<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;

/**
 * Validates that data is a proper JSON-encoded string
 */
class JsonRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data ) {

      if ( empty( $data ) || !is_string( $data ) ) {
        return false;
      }

      $decoded = @json_decode( $data, true ); // Unfortunately need to suppress errors

      return is_array( $decoded );

    } );

    $this->setClosure( $closure );

  } // __construct

} // JsonRule
