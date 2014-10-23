<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Not to be confused with the callback mechanism the rules are extending, this validator
 * will statically call a parameterized class, method name as the validation
 */
class CallbackRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( empty( $context['parameters'] ) || count( $context['parameters'] ) !== 2 ) {
        throw new RuleRequirementException( "Two parameters required for '" . __CLASS__ . "'" );
      }

      list( $object_name, $function_name ) = $context['parameters'];

      if ( !class_exists( $object_name ) ) {
        throw new InvalidRuleException( "Invalid class '{$object_name}' for callback" );
      }

      // Fire callback method - raw data and source key attached!
      // TODO: Check if method exists / is_callable, but in attempting to develop this, cyclically loaded the bootstrap for no reason (DS)
      return (bool)$object_name::$function_name( $data );

    } );

    $this->setClosure( $closure );

  } // __construct

} // CallbackRule
