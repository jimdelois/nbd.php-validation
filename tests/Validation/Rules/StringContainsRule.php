<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_StringContainsRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\StringContainsRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $input, $haystack, $pass_fail ) {

    $name = $this->_class;
    $rule = new $name();

    $context['parameters'] = [ $haystack ];

    $this->assertEquals( $pass_fail, $rule->isValid( $input, $context ) );

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
  public function invalidContextParameters() {

    $name = $this->_class;
    $rule = new $name();

    $value = 'abc';

    $rule->isValid( $value, [ 'parameters' => [ 123 ] ] );

  } // invalidContextParameters


  /**
   * @return array
   */
  public function testDataProvider() {

    return [
        [ 'abc', 'abcdefg', true ],
        [ 'abc', 'abcdef', true ],
        [ 'abc', 'abcde', true ],
        [ 'abc', 'abc', true ],
        [ 'abc', 'ab', false ],
        [ 'àbč', 'àbčdefg', true ],
        [ 'ÁBČ', 'àbčdefg', false ],
        [ 'ABC', 'abcdefg', false ],
        [ 'ÁBČ', 'ÁBČabcefg', true ],
        [ 'ÁBČ', 'abcÁBČdefg', true ],
        [ 'ÁBČ', 'abcdefgÁBČ', true ],
        [ 123, 'ab', false ],
        [ 'abc', 'ábcdefg', false ],
        [ [], 'ábcdefg', false ],
        [ ( new stdClass() ), 'ábcdefg', false ],
        [ ( function() {} ), 'ábcdefg', false ],
        [ true, '1', false ],
        [ false, '0', false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_StringContainsRuleTest
