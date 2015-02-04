<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Not to be confused with the callback mechanism the rules are extending, this validator
 * will statically call a parameterized class, method name as the validation
 */
class ArrayOfRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  public function __construct() {

    $closure = ( function( $parameters, array $context ) {

      if ( !is_array( $parameters ) ) {
        return false;
      }

      list( $inner_rule ) = $this->_extractContextParameters( $context );
      $validator          = $this->_extractContextValidator( $context );
      $provider           = $validator->getRulesProvider();

      try {

        $rule = $provider->getRule( $inner_rule );

      }
      catch( UnknownRuleException $e ) {

        throw new InvalidRuleException( 'Context parameter must be a defined rule' );

      }

      foreach ( $parameters as $parameter ) {

        if ( !$rule->isValid( $parameter, $context ) ) {
          return false;
        }

      } // foreach incoming parameter

      return true;

    } );

    $this->setClosure( $closure );

  } // __construct

} // ArrayOfRule
