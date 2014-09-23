<?php

namespace NBD\Validation\Exceptions\Validator;

use NBD\Validation\Exceptions\Exception;
use NBD\Validation\Interfaces\ValidatorServiceInterface;

class FailureException extends Exception {

  protected $_validator;

  /**
   * @param ValidatorServiceInterface $validator  failed service
   */
  public function setValidator( ValidatorServiceInterface $validator ) {

    $this->_validator = $validator;

  } // setValidator

  /**
   * @return ValidatorServiceInterface  when available
   */
  public function getValidator() {

    return $this->_validator;

  } // getValidator

} // FailureException
