<?php

use Behance\NBD\Validation\Providers\RulesProvider;

/**
 * @group validation
 */
class NBD_Validation_Providers_RulesProviderTest extends PHPUnit_Framework_TestCase {

  protected $_class      = 'Behance\NBD\Validation\Providers\RulesProvider',
            $_rule_class = 'Behance\NBD\Validation\Abstracts\RuleAbstract',
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
   * @expectedException Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException
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


  /**
   * @test
   */
  public function getSetRuleNamespaces() {

    $namespaces = $this->_rules->getRuleNamespaces();

    $this->assertCount( 1, $namespaces );

    $existing_namespace = $namespaces[0]; // Grab the value of the only known namespace
    $new_namespace      = 'Abc\\Def\\Ghi\\';

    $this->_rules->addRuleNamespace( $new_namespace );

    $namespaces = $this->_rules->getRuleNamespaces();

    $this->assertCount( 2, $namespaces );

    // Ensure new namespace is added AHEAD of existing namespace
    $this->assertEquals( $new_namespace,      $namespaces[0] );
    $this->assertEquals( $existing_namespace, $namespaces[1] );

  } // getSetRuleNamespaces


  /**
   * @test
   */
  public function normalizeRuleNamespace() {

    $normalized     = 'Normalized\\Namespace\\With\\Trailing\\';
    $non_normalized = 'Does\\Not\\Have\\Trailing';

    $this->_rules->addRuleNamespace( $normalized );
    $this->_rules->addRuleNamespace( $non_normalized );

    $namespaces = $this->_rules->getRuleNamespaces();

    $this->assertGreaterThan( 2, count( $namespaces ) ); // Two additional plus 1 existing

    $this->assertEquals( $non_normalized . '\\', $namespaces[0] ); // LIFO ensures this is first, normalized
    $this->assertEquals( $normalized,            $namespaces[1] ); // Already normalized, must match

  } // normalizeRuleNamespace


  /**
   * @test
   * @dataProvider parseRuleProvider
   */
  public function parseRule( $rules_string, $rules_expected ) {

    $validator = new \Behance\NBD\Validation\Services\ValidatorService();
    $provider  = $validator->getRulesProvider();

    $rules_actual = $provider->parseRulesDefinition( $rules_string );

    $this->assertEquals( $rules_expected, $rules_actual );

  } // parseRule


  /**
   * @return array
   */
  public function parseRuleProvider() {

    $rule_map       = [];

    $rules_as_array = [
        [ 'alpha' ],
        [ 'required', 'integer' ],
        [ 'required', 'arrayOf[alpha|maxLength[3]]' ],
        [ 'arrayOf[arrayOf[required|alpha|minLengh[1]]]' ],
        [ 'required', 'arrayOf[alpha|maxLength[3]]', 'maxLength[5]', 'arrayOf[arrayOf[required|alpha|minLengh[1]]]' ]
    ];

    foreach ( $rules_as_array as $rule_as_array ) {

      $rule_map[] = [
          'rules_string'   => join( '|', $rule_as_array ),
          'rules_expected' => $rule_as_array
      ];

    } // foreach rule

    return $rule_map;

  } // parseRuleProvider

  // TODO: Test for invalid rule formats and the provider ought through an exception.

} // NBD_Validation_Providers_RulesProviderTest
