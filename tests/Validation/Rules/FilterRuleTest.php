<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_FilterRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\FilterRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $input, $parameters, $pass_fail, $filtered ) {

    $name = $this->_class;
    $rule = new $name();

    $context['parameters'] = $parameters;

    $closure = $rule->getClosure();

    $this->assertEquals( $pass_fail, $closure( $input, $context, $input ) );

    $this->assertEquals( $filtered, $input );

  } // isValid


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function invalidParameters() {

    $name = $this->_class;
    $rule = new $name();

    $value = 123;

    $rule->isValid( $value, [], $value );

  } // invalidParameters


  /**
   * @test
   * @expectedException NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function invalidFilterFunction() {

    $name = $this->_class;
    $rule = new $name();

    $context['parameters'] = [ 'not-a-function' ];

    $value = 123;

    $rule->isValid( $value, $context, $value );

  } // invalidFilterFunction


  /**
   * @return array
   */
  public function testDataProvider() {

    $trimmable = '  abc  ';
    $object    = new stdClass();
    $closure   = ( function() {} );

    return [
        [ $trimmable, [ 'trim' ], true, trim( $trimmable ) ],
        [ $trimmable, [ 'rtrim' ], true, rtrim( $trimmable ) ],
        [ $trimmable, [ 'ltrim' ], true, ltrim( $trimmable ) ],
        [ $trimmable, [ 'trim', 'sha1', 'md5' ], true, md5( sha1( trim( $trimmable ) ) ) ],
        [ $trimmable, [ 'ltrim' ], true, ltrim( $trimmable ) ],
        [ $object, [ 'ltrim' ], false, $object ],
        [ $closure, [ 'ltrim' ], false, $closure ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_FilterRuleTest
