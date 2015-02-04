<?php

namespace Behance\NBD\Validation\Abstracts;

use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;

abstract class CallbackRuleAbstract extends RuleAbstract {

  protected $_closure;


  /**
   * @throws \Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   *
   * @return \Closure
   */
  public function getClosure() {

    if ( empty( $this->_closure ) ) {
      throw new RuleRequirementException( "Closure is required, use ->setClosure() first" );
    }

    return $this->_closure;

  } // getClosure


  /**
   * @param \Closure $closure
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
