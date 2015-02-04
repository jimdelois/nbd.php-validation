<?php

namespace Behance\NBD\Validation\Abstracts;

use Behance\NBD\Validation\Interfaces\RuleInterface;
use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;

abstract class RuleAbstract implements RuleInterface {

  // Names the key within context array where rule parameters are passed
  const KEY_CONTEXT_PARAMETERS = 'parameters';

  // When defined, mandates how many parameters must be present to return from context parameters
  const REQUIRED_PARAM_COUNT   = false;


  /**
   * Child classes can override this to change validation error message on $this rule
   * Use REPLACEMENT_PLACEHOLDER to define variables for replacement via context
   *
   * @var string
   */
  protected $_error_template = "%fieldname% failed validation";


  /**
   * {@inheritDoc}
   */
  public function getErrorTemplate() {

    return $this->_error_template;

  } // getErrorTemplate


  /**
   * Sets the template for generating an error message based for the current rule
   *
   * @param string $message
   */
  public function setErrorTemplate( $message ) {

    $this->_error_template = $message;

  } // setErrorTemplate


  /**
   * {@inheritDoc}
   */
  public function convertFormattingContext( array $context ) {

    return $context;

  } // convertFormattingContext

  /**
   * When a REQUIRED_PARAM_COUNT constant is defined, requires that many
   * items from context parameters and returns them to the caller
   *
   * @throws RuleRequirementException
   *
   * @param array $context
   *
   * @return array
   */
  protected function _extractContextParameters( array $context ) {

    $key             = static::KEY_CONTEXT_PARAMETERS;
    $required_params = static::REQUIRED_PARAM_COUNT;

    if ( empty( $context[ $key ] ) ) {
      throw new RuleRequirementException( "Parameters required for '" . get_class( $this ) . "'" );
    }

    if ( $required_params !== false && count( $context[ $key ] ) !== $required_params ) {
      throw new RuleRequirementException( "{$required_params} parameters required for '" . get_class( $this ) . "'" );
    }

    return $context[ $key ];

  } // _extractContextParameters

  const KEY_CONTEXT_VALIDATOR = 'validator';

  protected function _extractContextValidator( array $context ) {

    $key = static::KEY_CONTEXT_VALIDATOR;

    if ( empty( $context[ $key ] ) ) {
      throw new RuleRequirementException( "Context Validator required for '" . get_class( $this ) . "'" );
    }

    return $context[ $key ];

  } // _extractContextValidator

} // RuleAbstract
