<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_AlphaRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\AlphaRule';

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
        [ 'ábčabc123', false ],
        [ 'ÁBÇabc123', false ],
        [ '', false ],
        [ 0, false ],
        [ true, false ],
        [ 'true', true ],
        [ false, false ],
        [ 'false', true ],
        [ 123, false ],
        [ 456, false ],
        [ 789, false ],
        [ ( new stdClass() ), false ],
        [ ( function() {} ), false ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_AlphaRuleTest
