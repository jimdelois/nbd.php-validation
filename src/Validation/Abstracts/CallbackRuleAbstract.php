<?php

namespace NBD\Validation\Abstracts;

use NBD\Validation\Abstracts\RuleAbstract;

use NBD\Validation\Exceptions\Validator\RuleRequirementException;

abstract class CallbackRuleAbstract extends RuleAbstract {

  protected $_closure;


  /**
   * @return Closure
   */
  public function getClosure() {

    if ( empty( $this->_closure ) ) {
      throw new RuleRequirementException( "Closure is required, use ->setClosure() first" );
    }

    return $this->_closure;

  } // getClosure


  /**
   * @param Closure $closure
   */
  public function setClosure( \Closure $closure ) {

    $this->_closure = $closure;

  } // setClosure


  /**
   * @inheritDoc
   */
  public function isValid( $data, array $context = null ) {

    $closure = $this->getClosure();

    return (bool)$closure( $data, $context );

  } // isValid

} // CallbackRuleAbstract
