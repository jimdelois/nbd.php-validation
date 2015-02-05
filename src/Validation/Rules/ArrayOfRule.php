<?php

namespace Behance\NBD\Validation\Rules;

use \Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use \Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException;
use \Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * This rule will ensure that the incoming data is an array, then recursively
 * parse all "inner" rules and apply them to each element within the array
 */
class ArrayOfRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( !is_array( $data ) ) {
        return false;
      }

      list( $inner_definition ) = $this->_extractContextParameters( $context );

      $field                    = $this->_extractContextField( $context );
      $validator                = $this->_extractContextValidator( $context );
      $provider                 = $validator->getRulesProvider();

      $rule_cache               = []; // Local cache map
      $inner_definitions        = $provider->parseRulesDefinition( $inner_definition );

      foreach ( $data as $datum ) {

        foreach ( $inner_definitions as $definition ) {

          list( $inner_rule_name, $inner_rule_parameters ) = $provider->processRuleIntoFunctionAndArguments( $definition, $field );

          // Update the context for the next inner rule to use while validating
          $sub_context               = $context;
          $sub_context['rule_name']  = $inner_rule_name;
          $sub_context['parameters'] = $inner_rule_parameters;

          // Local cache to avoid reloading common rules
          if ( !isset( $rule_cache[ $inner_rule_name ] ) ) {

            try {
              $rule_cache[ $inner_rule_name ] = $provider->getRule( $inner_rule_name );
            }
            catch( UnknownRuleException $e ) {
              throw new InvalidRuleException( 'Context parameter must be a defined rule' );
            }

          } // if rule isn't loaded

          $rule = $rule_cache[ $inner_rule_name ];

          if ( !$rule->isValid( $datum, $sub_context ) ) {

            return false;

          }

        } // foreach rule

      } // foreach incoming parameter

      return true;

    } );

    $this->setClosure( $closure );

  } // __construct


} // ArrayOfRule
