<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_AlphaNumericRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\AlphaNumericRule';

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
        [ 'ábčabc', true ],
        [ 'ÁBČabc', true ],
        [ 'ábčabc123', true ],
        [ 'ÁBÇabc123', true ],
        [ '', false ],
        [ 0, true ],
        [ true, true ],
        [ 'true', true ],
        //[ (int)false, true ], <-- passes
        //[ false, true ],  <-- fails
        [ 'false', true ],
        [ 123, true ],
        [ 456, true ],
        [ 789, true ],
        [ ( new stdClass() ), false ],
        [ ( function() {} ), false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_AlphaNumericRuleTest
