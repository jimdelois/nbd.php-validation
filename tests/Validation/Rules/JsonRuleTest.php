<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_JsonRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\JsonRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $data, $expected ) {

    $name = $this->_class;
    $rule = new $name();

    $this->assertEquals( $expected, $rule->isValid( $data ) );

  } // isValid


  /**
   * @see https://code.google.com/p/json-smart/wiki/FeaturesTests
   *
   * @return array
   */
  public function testDataProvider() {

    return [
        [ '{}', true ],
        [ '{ "v":"1"}', true ],
        [ '{  "v":"1"' . "\r\n" . '}', true ],
        [ '{ "v":1}', true ],
        [ '{ "v":"ab\'c"}', true ],
        [ '{ "PI":3.141E-10}', true ],
        [ '{ "PI":3.141e-10}', true ],
        [ '{ "v":12345123456789}', true ],
        [ '{ "v":123456789123456789123456789}', true ],
        [ '[ 1,2,3,4]', true ],
        [ '[ "1","2","3","4"]', true ],
        [ '[ { }, { },[]]', true ],
        [ '{ "v":"\u2000\u20FF"}', true ],
        [ '{ "v":"\u2000\u20ff"}', true ],
        [ '{ "a":"hp://foo"}', true ],
        [ '{ "a":null}', true ],
        [ '{ "a":true}', true ],
        [ '{ "a" : true }', true ],
        [ '{ "v":1.7976931348623157E308}', true ],
        [ '{\'X\':\'s', false ],
        [ '{\'X', false ],
        [ 1, false ],
        [ -1, false ],
        [ 10000, false ],
        [ PHP_INT_MAX, false ],
        [ -PHP_INT_MAX, false ],
        [ -10000, false ],
        [ 0, false ],
        [ "0", false ],
        [ "1", false ],
        [ "-", false ],
        [ 1.01, false ],
        [ -1.01, false ],
        [ "1.0", false ],
        [ "-1.0", false ],
        [ 1.43e26, false ],
        [ "1.43e26", false ],
        [ 'false', false ],
        [ 'false', false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_JsonRuleTest
