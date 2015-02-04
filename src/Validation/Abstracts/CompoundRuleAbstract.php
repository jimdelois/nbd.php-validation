<?php

namespace Behance\NBD\Validation\Abstracts;

use Behance\NBD\Validation\Interfaces\CompoundRuleInterface;
use Behance\NBD\Validation\Interfaces\RulesProviderInterface;

abstract class CompoundRuleAbstract extends CallbackRuleAbstract implements CompoundRuleInterface {

  protected $_provider;

  /**
   * {@inheritdoc}
   */
  public function setRulesProvider( RulesProviderInterface $provider ) {

    $this->_provider = $provider;

  } // setRulesProvider

  /**
   * Internal method for retrieving the RulesProvider injected, if any.
   *
   * @return \Behance\NBD\Validation\Interfaces\RulesProviderInterface
   */
  protected function _getRulesProvider() {

    return $this->_provider;

  } // _getRulesProvider

} // CompoundRuleAbstract
