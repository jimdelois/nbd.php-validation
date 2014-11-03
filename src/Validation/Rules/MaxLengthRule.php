<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\CallbackRuleAbstract;
use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;

/**
 * Validates that data is a string over a certain length of characters
 */
class MaxLengthRule extends CallbackRuleAbstract {

  const REQUIRED_PARAM_COUNT = 1;

  protected $_error_template = "%fieldname% must be %length% characters or less";

  /**
   * @inheritDoc
   */
  public function __construct() {

    $closure = ( function( $data, array $context ) {

      if ( !is_string( $data ) ) {
        return false;
      }

      $length = mb_strlen( $data, 'UTF-8' );

      list( $max_length ) = $this->_extractContextParameters( $context );

      // Easily ensure this is a positive integer
      if ( !ctype_digit( (string)$max_length ) ) {
        throw new InvalidRuleException( "Maximum length must be a positive integer" );
      }

      return ( $length <= $max_length );

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


} // MaxLengthRule
