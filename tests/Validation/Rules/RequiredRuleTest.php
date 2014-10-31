<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_RequiredRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\RequiredRule';

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
   * @return array
   */
  public function testDataProvider() {

    return [
        [ 'abc', true ],
        [ 'ábč', true ],
        [ '', true ],
        [ 0, true ],
        [ true, true ],
        [ 'true', true ],
        [ false, true ],
        [ 'false', true ],
        [ 123, true ],
        [ ( new stdClass() ), true ],
        [ ( function() {} ), true ],
        [ null, false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_RequiredRuleTest
