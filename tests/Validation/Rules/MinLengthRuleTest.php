<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_MinLengthRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\MinLengthRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $data, $max, $expected ) {

    $name = $this->_class;
    $rule = new $name();

    $context['parameters'] = [ $max ];

    $this->assertEquals( $expected, $rule->isValid( $data, $context ) );

  } // isValid


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function invalidParameters() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $rule->isValid( $value, [] );

  } // invalidParameters


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function invalidLengthParameter() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $context['parameters'] = [ 'efg' ];

    $rule->isValid( $value, $context );

  } // invalidLengthParameter


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function negativeLengthParameter() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $context['parameters'] = [ -3 ];

    $rule->isValid( $value, $context );

  } // negativeLengthParameter


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function negativeStringLengthParameter() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $context['parameters'] = [ '-3' ];

    $rule->isValid( $value, $context );

  } // negativeStringLengthParameter


  /**
   * @return array
   */
  public function testDataProvider() {

    return [
        [ 'abc', 4, false ],
        [ 'abc', '4', false ],
        [ 'abc', 3, true ],
        [ 'abc', '3', true ],
        [ 'abc', 2, true ],
        [ 'abc', '2', true ],
        [ 'abc', 1, true ],
        [ 'abc', '1', true ],
        [ 'abc', 0, true ],
        [ 'abc', '0', true ],
        [ 'ábč', 4, false ],
        [ 'ábč', '4', false ],
        [ 'ábč', 3, true ],
        [ 'ábč', '3', true ],
        [ 'ábč', 2, true ],
        [ 'ábč', '2', true ],
        [ 'ábč', 1, true ],
        [ 'ábč', '1', true ],
        [ 'ábč', 0, true ],
        [ 'ábč', '0', true ],
        [ '', 0, true ],
        [ '', 1, false ],
        [ 123, 123, false ],
        [ 456, 1, false ],
        [ 789, 1000, false ],
        [ ( new stdClass() ), 5, false ],
        [ ( function() {} ), 4, false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_MinLengthRuleTest
