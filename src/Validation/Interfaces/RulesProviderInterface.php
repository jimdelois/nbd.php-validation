<?php

namespace NBD\Validation\Interfaces;

use NBD\Validation\Interfaces\RuleInterface;

interface RulesProviderInterface {

  /**
   * Retrieves rule based on $name
   *
   * @param string $name
   *
   * @return NBD\Validation\Interfaces\RuleInterface
   */
  public function getRule( $name );


  /**
   * @param string        $name  how to identify $rule
   * @param RuleInterface $rule  custom validation
   */
  public function setRule( $name, RuleInterface $rule );


  /**
   * Convenience function to build and set a validator based on a regex pattern
   *
   * @param string $name     how to reference rule in future
   * @param string $pattern  regex to evaluate for this rule
   *
   * @return NBD\Validation\Rules\Templates\RegexTemplateRule
   */
  public function setRegexRule( $name, $pattern );


  /**
   * TODO: convert from callable to callback rule class
   *
   * @param string  $name     how to identify rule
   * @param Closure $closure  processes validator data
   *
   * @return NBD\Validation\Rules\Templates\CallbackTemplateRule
   */
  public function setCallbackRule( $name, \Closure $closure );


} // RulesProviderInterface
