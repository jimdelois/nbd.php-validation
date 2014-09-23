<?php

namespace NBD\Validation\Providers;

use NBD\Validation\Interfaces\RulesProviderInterface;
use NBD\Validation\Interfaces\RuleInterface;

use NBD\Validation\Rules\Templates\RegexTemplateRule;
use NBD\Validation\Rules\Templates\CallbackTemplateRule;

use NBD\Validation\Exceptions\Rules\NoSuchRuleException;

/**
 * Stores rules as callbacks that can be accessed by key
 */
class RulesProvider implements RulesProviderInterface {

  // const PREFIX_CALLBACK = '_callback'; // Prepended to callback rules that are locally defined

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
   * @param Closure $closure
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
   * @throws NoSuchRuleException
   *
   * @param string $name
   *
   * @return NBD\Validation\Interfaces\RuleInterface
   */
  protected function _buildStandardRule( $name ) {

    $class_name = "NBD\\Validation\\Rules\\" . ucfirst( $name ) . 'Rule';

    if ( !class_exists( $class_name, true ) ) {
      throw new NoSuchRuleException( "Rule '{$name}' is not a validator rule" );
    }

    return new $class_name();

  } // _buildStandardRule

} // RulesProvider
