<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_ContainedInRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\ContainedInRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $input, $parameters, $expected_outcome ) {

    $name = $this->_class;
    $rule = new $name();

    $context['parameters'] = $parameters;

    $this->assertEquals( $expected_outcome, $rule->isValid( $input, $context ) );

  } // isValid


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function invalidParameters() {

    $name = $this->_class;
    $rule = new $name();

    $rule->isValid( 123, [] );

  } // invalidParameters


  /**
   * @return array
   */
  public function testDataProvider() {

    return [
        [ 'abc', [ 'abc', 'def', 'ghi' ], true ],
        [ 'abc', [ 'def', 'abc', 'ghi' ], true ],
        [ 'abc', [ 'def', 'ghi', 'abc' ], true ],
        [ 'abc', [ 'abc' ], true ],
        [ 'abc', [ 'def', 'ghi' ], false ],
        [ 123, [ 'abc', 'def', 'ghi' ], false ],
        [ 123, [ 123, 'def', 'ghi' ], true ],
        [ 123, [ 'abc', 123, 'ghi' ], true ],
        [ 123, [ 'abc', 'def', 123 ], true ],
        [ [ 123 ], [ 'abc', 'def', 123 ], false ],
        [ new stdClass(), [ 'abc', 'def', 'ghi' ], false ],
        [ ( function() {} ), [ 'abc', 'def', 'ghi' ], false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_ContainedInRuleTest
