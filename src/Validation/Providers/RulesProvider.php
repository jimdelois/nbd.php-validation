<?php

namespace Behance\NBD\Validation\Providers;

use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;
use Behance\NBD\Validation\Interfaces\RulesProviderInterface;
use Behance\NBD\Validation\Interfaces\RuleInterface;

use Behance\NBD\Validation\Rules\Templates\RegexTemplateRule;
use Behance\NBD\Validation\Rules\Templates\CallbackTemplateRule;

use Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException;

/**
 * Stores rules as callbacks that can be accessed by key
 */
class RulesProvider implements RulesProviderInterface {

  const RULE_NAME_SUFFIX = 'Rule';
  const RULE_COMPOUND    = '\Behance\NBD\Validation\Interfaces\CompoundRuleInterface';

  /**
   * @var array  different namespaces to use for locating rules
   */
  protected $_rule_namespaces = [
      'Behance\\NBD\\Validation\\Rules\\'
  ];

  /**
   * @var array storage for user-defined callback validators
   */
  protected $_rules;


  /**
   * {@inheritDoc}
   */
  public function getRule( $name ) {

    if ( !empty( $this->_rules[ $name ] ) ) {
      return $this->_rules[ $name ];
    }

    return $this->_buildStandardRule( $name );

  } // getRule


  /**
   * {@inheritDoc}
   */
  public function setRule( $name, RuleInterface $rule ) {

    $this->_rules[ $name ] = $rule;

  } // setRule


  /**
   * Convenience function to build and set a validator based on a regex pattern
   *
   * @param string $name     how to reference rule in future
   * @param string $pattern  regex to evaluate for this rule
   *
   * @return RulesTemplateRule
   */
  public function setRegexRule( $name, $pattern ) {

    $rule = $this->_buildRegexRule( $pattern );

    $this->setRule( $name, $rule );

    return $rule;

  } // setRegexRule


  /**
   * Convenience function to build and set a validator based on a callback closure
   *
   * @param string   $name      how to identify rule
   * @param callable $callback  processes validator data
   */
  public function setCallbackRule( $name, \Closure $callback ) {

    $rule = $this->_buildCallbackRule( $callback );

    $this->setRule( $name, $rule );

    return $rule;

  } // setCallbackRule


  /**
   * IMPORTANT: namespace list and rule definition is LIFO (last in first out)
   * Will add $namespace to list of currently defined namespaces, taking priority over previously defined ones
   *
   * @param string $namespace  forms the base for classnames that conform to {namespace}\{rule_name}Rule.php
   *                           and implement Behance\NBD\Validation\Interfaces\RuleInterface
   */
  public function addRuleNamespace( $namespace ) {

    $separator = '\\';

    // Normalize rule namespace to always end with the namespace separator
    $namespace = rtrim( $namespace, $separator ) . $separator;

    array_unshift( $this->_rule_namespaces, $namespace );

  } // addRuleNamespace


  /**
   * @return array  all currently defined namespaces
   */
  public function getRuleNamespaces() {

    return $this->_rule_namespaces;

  } // getRuleNamespaces


  /**
   * @param string $pattern
   *
   * @return RegexTemplateRule
   */
  protected function _buildRegexRule( $pattern ) {

    $rule = new RegexTemplateRule();
    $rule->setPattern( $pattern );

    return $rule;

  } // _buildRegexRule


  /**
   * @param \Closure $closure
   *
   * @return CallbackTemplateRule
   */
  protected function _buildCallbackRule( \Closure $closure ) {

    $rule = new CallbackTemplateRule();
    $rule->setClosure( $closure );

    return $rule;

  } // _buildCallbackRule


  /**
   * Creates an instance of a built-in rule based on $name
   *
   * @throws UnknownRuleException
   *
   * @param string $name
   *
   * @return \Behance\NBD\Validation\Interfaces\RuleInterface
   */
  protected function _buildStandardRule( $name ) {

    $namespaces = $this->getRuleNamespaces();

    foreach ( $namespaces as $namespace ) {

      $class_name = $namespace . ucfirst( $name ) . self::RULE_NAME_SUFFIX;

      if ( !class_exists( $class_name, true ) ) {
        continue;
      }

      // Class is located, return right away
      return new $class_name();

    } // foreach namespaces

    // After going through all possible namespaces, nothing was found, fail
    throw new UnknownRuleException( "Rule '{$name}' is not a validator rule" );

  } // _buildStandardRule


  /**
   * @param mixed  $rule
   * @param string $field  what is currently being processed
   *
   * @return array [ 0 => function name/Closure, 1 => optional array of parameters ]
   */
  public function processRuleIntoFunctionAndArguments( $rule, $field ) {

    $rule_parameters = [];

    if ( $rule instanceof \Closure ) {
      $rule = $this->_convertToCallableName( $rule );
    }

    else {
      // When a [ appears anywhere, this is an attempt to use a rule as parameterized function
      $param_position = strpos( $rule, '[' );

      if ( $param_position !== false )  {

        // When there's no complimenting bracket, this is a problem
        if ( substr( $rule, -1 ) !== ']' )  {
          throw new RuleRequirementException( "Field '{$field}' needs rule parameters encapsulated by []" );
        }

        // Remove the brackets from the request, leaving a (hopefully) comma-separated list of parameters
        $rule_arguments  = substr( $rule, ( $param_position + 1 ), strlen( $rule ) );

        // Remove parameters from the rule name
        $rule            = substr( $rule, 0, $param_position );
        $rule_arguments  = substr( $rule_arguments, 0, ( strlen( $rule_arguments ) - 1 ) );

        // Create an array by dividing arguments along the comma
        $rule_parameters = explode( ',', $rule_arguments );

      } // if param_position

    } // else (!closure)

    // Standardize rules with lowercase first character
    return [ lcfirst( $rule ), $rule_parameters ];

  } // processRuleIntoFunctionAndArguments


  /**
   * Provides backwards compatibility for existing callback rules
   *
   * @param Closure $rule
   *
   * @return string
   */
  protected function _convertToCallableName( \Closure $rule ) {

    // TODO: move this assignment implementation into RulesProvider
    $new_name = spl_object_hash( $rule );

    $this->setCallbackRule( $new_name, $rule );

    return $new_name;

  } // _convertToCallableName

} // RulesProvider
