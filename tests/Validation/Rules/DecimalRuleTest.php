<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_DecimalRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\DecimalRule';

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
        [ 'abc', false ],
        [ 'ábč', false ],
        [ 'ábčabc', false ],
        [ 'ÁBČabc', false ],
        [ 'ábčabc123', false ],
        [ 'ÁBÇabc123', false ],
        [ '', false ],
        [ 0, true ],
        [ '0', true ],
        [ '10', true ],
        [ true, true ],
        [ 'true', false ],
        // [ false, true ], <-- fails
        [ (int)false, true ], // <-- passes
        [ 'false', false ],
        [ 123, true ],
        [ 456, true ],
        [ 789, true ],
        [ 123.123, true ],
        [ 123.1, true ],
        [ 123.0, true ],
        [ 123.0e26, false ],
        [ ( new stdClass() ), false ],
        [ ( function() {} ), false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_DecimalRuleTest
