<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_ArrayRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\ArrayRule';

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
        [ 0, false ],
        [ true, false ],
        [ 'true', false ],
        [ false, false ],
        [ 'false', false ],
        [ 123, false ],
        [ ( new stdClass() ), false ],
        [ ( function() {} ), false ],
        [ [], true ],
        [ [ 'abc' ], true ],
        [ [ 'abc' => 123 ], true ],
        [ [ 0 => 'abc' ], true ],
        [ [ 0 => 'abc', 1 => 123, 'def' => true ], true ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_ArrayRuleTest
