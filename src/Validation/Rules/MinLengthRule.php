<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Validates that data is a string over a certain length of characters
 */
class MinLengthRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  protected $_error_template = "%fieldname% must be %length% characters or more";

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( !is_string( $data ) ) {
        return false;
      }

      $length = mb_strlen( $data, 'UTF-8' );

      list( $min_length ) = $this->_extractContextParameters( $context );

      // Easily ensure this is a positive integer
      if ( !ctype_digit( (string)$min_length ) ) {
        throw new InvalidRuleException( "Minimum length must be a positive integer" );
      }

      return ( $length >= $min_length );

    } );

    $this->setClosure( $closure );

  } // __construct


  /**
   * {@inheritDoc}
   */
  public function convertFormattingContext( array $context ) {

    list( $length )    = $this->_extractContextParameters( $context );
    $context['length'] = $length;

    return $context;

  } // convertFormattingContext


} // MinLengthRule
