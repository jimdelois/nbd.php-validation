<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_InstanceOfRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\InstanceOfRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $input, $class_name, $pass_fail ) {

    $name = $this->_class;
    $rule = new $name();

    $context['parameters'] = [ $class_name ];

    $this->assertEquals( $pass_fail, $rule->isValid( $input, $context ) );

  } // isValid


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function invalidParameters() {

    $name  = $this->_class;
    $rule  = new $name();
    $value = ( function() {} );

    $rule->isValid( $value, [] );

  } // invalidParameters


  /**
   * @return array
   */
  public function testDataProvider() {

    $class_name = $this->_class;

    return [
        [ $this, __CLASS__, true ],
        [ ( new stdClass() ), 'stdClass', true ],
        [ new $class_name(), $class_name, true ],
        [ ( function() {} ), 'Closure', true ],
        [ 'abc', 'string', false ],
        [ 123, 'integer', false ],
        [ 'abc', __CLASS__, false ],
        [ 123, __CLASS__, false ],
        [ [], __CLASS__, false ],
        [ false, __CLASS__, false ],
        [ true, __CLASS__, false ],
        [ 50.25, __CLASS__, false ],
        [ 50.25e26, __CLASS__, false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_InstanceOfRuleTest
