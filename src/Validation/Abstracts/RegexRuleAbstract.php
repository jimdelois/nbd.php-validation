<?php

namespace Behance\NBD\Validation\Abstracts;

use Behance\NBD\Validation\Abstracts\RuleAbstract;

abstract class RegexRuleAbstract extends RuleAbstract {

  protected $_pattern;

  /**
   * @return string
   */
  public function getPattern() {

    return $this->_pattern;

  } // getPattern

  /**
   * @param string $pattern
   */
  public function setPattern( $pattern ) {

    $this->_pattern = $pattern;

  } // setPattern

  /**
   * @inheritDoc
   */
  public function isValid( $data, array $context = null ) {

    $context; // Satisfy PHPMD

    if ( is_object( $data ) ) {
      return false;
    }

    return (bool)preg_match( $this->getPattern(), $data );

  } // isValid

} // RegexRuleAbstract
