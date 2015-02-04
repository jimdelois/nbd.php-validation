<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CompoundRuleAbstract;
use Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Not to be confused with the callback mechanism the rules are extending, this validator
 * will statically call a parameterized class, method name as the validation
 */
class ArrayOfRule extends CompoundRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  public function __construct() {

    $closure = ( function( $parameter, array $context ) {

      if ( !is_array( $parameter ) ) {
        return false;
      }

      list( $inner_rule ) = $this->_extractContextParameters( $context );

      try {
//        $rule = $this->_getRulesProvider()->getRule( $inner_rule );
        $validator = $this->_extractContextValidator( $context );
        $provider  = $validator->getRulesProvider();

        $field = $context['field']; // TODO: "_extractContextField"

        list( $rule_name, $rule_parameters ) = $provider->processRuleIntoFunctionAndArguments( $inner_rule, $field );

        $rule      = $provider->getRule( $rule_name );

        $new_context = $context;
        $new_context['rule_name']  = $rule_name;
        $new_context['parameters'] = $rule_parameters;

      }
      catch( UnknownRuleException $e ) {
        throw new InvalidRuleException( 'Context parameter must be a defined rule' );
      }

      foreach ( $parameter as $value ) {

        if ( !$rule->isValid( $value, $new_context ) ) {
          return false;
        }

      } // foreach incoming parameter

      return true;

    } );

    $this->setClosure( $closure );

  } // __construct

} // ArrayOfRule
