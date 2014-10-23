<?php

namespace NBD\Validation\Exceptions\Validator;

use NBD\Validation\Exceptions\Exception;
use NBD\Validation\Interfaces\ValidatorServiceInterface;

class FailureException extends Exception {

  protected $_validator;

  /**
   * {@inheritDoc}
   */
  public function __construct( $message = '', $code = 0, \Exception $previous = null, ValidatorServiceInterface $validator = null ) {

    parent::__construct( $message, $code, $previous );

    if ( $validator ) {
      $this->_setValidator( $validator );
    }

  } // __construct


  /**
   * @return ValidatorServiceInterface  when available
   */
  public function getValidator() {

    return $this->_validator;

  } // getValidator


  /**
   * @param ValidatorServiceInterface $validator  failed service
   */
  private function _setValidator( ValidatorServiceInterface $validator ) {

    $this->_validator = $validator;

  } // _setValidator

} // FailureException
