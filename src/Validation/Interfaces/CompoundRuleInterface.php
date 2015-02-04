<?php

namespace Behance\NBD\Validation\Interfaces;

interface CompoundRuleInterface extends RuleInterface {

  /**
   * Inject the Rules Provider so this rule can access others at runtime.
   *
   * @param \Behance\NBD\Validation\Interfaces\RulesProviderInterface $provider The Rules Provider
   *
   * @return null
   */
  public function setRulesProvider( RulesProviderInterface $provider );

} // CompoundRuleInterface
