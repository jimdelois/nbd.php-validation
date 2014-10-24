<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_MaxLengthRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\MaxLengthRule';

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
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function invalidParameters() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $rule->isValid( $value, [] );

  } // invalidParameters


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
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
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function negativeLengthParameter() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $context['parameters'] = [ -3 ];

    $rule->isValid( $value, $context );

  } // negativeLengthParameter

  /**
   * @return array
   */
  public function testDataProvider() {

    return [
        [ 'abc', 4, true ],
        [ 'abc', 3, true ],
        [ 'abc', 2, false ],
        [ 'abc', 1, false ],
        [ 'abc', 0, false ],
        [ 'ábč', 4, true ],
        [ 'ábč', 3, true ],
        [ 'ábč', 2, false ],
        [ 'ábč', 1, false ],
        [ 'ábč', 0, false ],
        [ '', 0, true ],
        [ '', 1, true ],
        [ 123, 123, false ],
        [ 456, 1, false ],
        [ 789, 1000, false ],
        [ ( new stdClass() ), 5, false ],
        [ ( function() {} ), 4, false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_MaxLengthRuleTest
