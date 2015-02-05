<?php

namespace Behance\NBD\Validation\Interfaces;

interface RulesProviderInterface {

  /**
   * Retrieves rule based on $name
   *
   * @param string $name
   *
   * @return Behance\NBD\Validation\Interfaces\RuleInterface
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
   * @return Behance\NBD\Validation\Rules\Templates\RegexTemplateRule
   */
  public function setRegexRule( $name, $pattern );


  /**
   * TODO: convert from callable to callback rule class
   *
   * @param string  $name     how to identify rule
   * @param Closure $closure  processes validator data
   *
   * @return Behance\NBD\Validation\Rules\Templates\CallbackTemplateRule
   */
  public function setCallbackRule( $name, \Closure $closure );


  /**
   * Will add $namespace to list of currently defined namespaces.
   * Implementations should arrange the list as LIFO (last in first out)
   *
   * @param string $namespace  the bucket used to organize rules
   */
  public function addRuleNamespace( $namespace );


  /**
   * Parses a string of delimited, potentially-nested rule definitions
   * into an array of top-level rule definition strings based on the
   * provider's rule specification syntax
   *
   * @param string $definition The string of rules to be parsed
   *
   * @return string[]
   */
  public function parseRulesDefinition( $definition );


  /**
   * Converts a single rule definition into a callable name or rule identifier along
   * with an array of any parameters to be used when applying the rule, based on
   * the provider's rule specification syntax
   *
   * TODO: A major version increase would be necessary, but it would be extremely
   *  useful to create a "Context" object and return that from this method. Otherwise
   *  we may also benefit from breaking this out into two interface definitions -
   *  one to extract the rule name and another to extract the parameters, which would
   *  not require a major version increase.
   *
   * @param mixed  $rule   The rule object to parse
   * @param string $field  The field for which the rule is currently being processed
   *
   * @return array [ 0 => function name/Closure, 1 => optional array of parameters ]
   */
  public function processRuleIntoFunctionAndArguments( $rule, $field );

} // RulesProviderInterface
