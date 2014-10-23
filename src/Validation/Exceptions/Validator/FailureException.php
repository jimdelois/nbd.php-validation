<?php

namespace Behance\NBD\Validation\Exceptions\Validator;

use Behance\NBD\Validation\Exceptions\Exception;
use Behance\NBD\Validation\Interfaces\ValidatorServiceInterface;

class FailureException extends Exception {

  protected $_validator;

  /**
   * {@inheritDoc}
   */
  public function __construct( $message = '', $code = 0, \Exception $previous = null, ValidatorServiceInterface $validator = null ) {

    parent::__construct( $message, $code, $previous );

    if ( $validator ) {
      $this->_validator = $validator;
    }

  } // __construct


  /**
   * @return ValidatorServiceInterface  when available
   */
  public function getValidator() {

    return $this->_validator;

  } // getValidator

} // FailureException
