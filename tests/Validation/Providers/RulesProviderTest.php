<?php

use NBD\Validation\Providers\RulesProvider;

/**
 * @group validation
 */
class NBD_Validation_Providers_RulesProviderTest extends PHPUnit_Framework_TestCase {

  protected $_class      = 'NBD\Validation\Providers\RulesProvider',
            $_rule_class = 'NBD\Validation\Abstracts\RuleAbstract',
            $_rules;

  protected function setUp() {

    $this->_rules = new RulesProvider();

  } // setUp


  /**
   * @test
   */
  public function getRuleStandard() {

    $rule = $this->_rules->getRule( 'integer' );

    $this->assertInstanceOf( $this->_rule_class, $rule );

  } // getRuleStandard



  /**
   * @test
   */
  public function addRegexRule() {

    $key     = 'abcdef';
    $pattern = '/^abcdef$/';

    $rule    = $this->_rules->setRegexRule( $key, $pattern );

    $this->assertSame( $rule, $this->_rules->getRule( $key ) );

    $this->assertEquals( $pattern, $rule->getPattern() );

  } // addRegexRule


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Rules\NoSuchRuleException
   */
  public function getRegexRuleDoesntExist() {

    $this->_rules->getRule( 'rule-doesnt-exist' );

  } // getRegexRuleDoesntExist


  /**
   * @test
   */
  public function getSetCallbackRule() {

    $rules    = $this->_rules;
    $rule_key = 'myNewRule';
    $expected = 'arbitrary_result';
    $callback = ( function( $data ) use ( $expected ) {

      // Appease PHPMD
      $data;

      return $expected;

    } );


    $rules->setCallbackRule( $rule_key, $callback );

    $this->assertSame( $callback, $rules->getRule( $rule_key )->getClosure() );

    $this->assertEquals( $expected, $callback( 1, 2 ) );

  } // getSetCallbackRule


} // NBD_Validation_Providers_RulesProviderTest
