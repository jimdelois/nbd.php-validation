<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\CallbackRuleAbstract;
use NBD\Validation\Exceptions\Validator\RuleRequirementException;
use NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Transforms incoming data to be used for the remainder of validation chain
 */
class FilterRule extends CallbackRuleAbstract {

  /**
   * @inheritDoc
   */
  public function __construct() {

    // IMPORTANT: $raw_data is pass by reference, save transformed output there
    $closure = ( function( $data, array $context, &$raw_data = null ) {

      if ( empty( $context['parameters'] ) || !is_array( $context['parameters'] ) ) {
        throw new RuleRequirementException( "Parameters required for '" . __CLASS__ . "'" );
      }

      if ( is_array( $data ) || is_object( $data ) ) {
        return false;
      }

      $filter_functions = $context['parameters'];

      foreach ( $filter_functions as $filter ) {

        if ( !function_exists( $filter ) ) {
          throw new InvalidRuleException( "Undefined filter function '{$filter}'" );
        }

        $data = $filter( $data );

      } // foreach rule_parameters

      // Filtered data will be saved to field, next rules will see this only
      $raw_data = $data;

      return true;

    } );

    $this->setClosure( $closure );

  } // __construct

} // FilterRule
