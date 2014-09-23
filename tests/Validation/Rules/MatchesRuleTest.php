<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_MatchesRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'NBD\Validation\Rules\MatchesRule';

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $field1, $field2, $expected ) {

    $name = $this->_class;
    $rule = new $name();

    $closure = $rule->getClosure();

    $this->assertEquals( $expected, $closure( $field1, $field2 ) );

  } // isValid


  /**
   * @return array
   */
  public function testDataProvider() {

    $class1 = new stdClass();
    $class2 = new stdClass();
    $func1  = ( function() {} );
    $func2  = ( function() {} );

    return [
        [ 'abc', 'abc', true ],
        [ 'ábč', 'ábč', true ],
        [ 'ÁBČ', 'ÁBČ', true ],
        [ 'ABC', 'ÁBČ', false ],
        [ 'ÁBČ', 'ABC', false ],
        [ 123, 123, true ],
        [ 123, '123', false ],
        [ '123', 123, false ],
        [ '123', '123', true ],
        [ '', false, false ],
        [ false, '', false ],
        [ '', 0, false ],
        [ 0, '', false ],
        [ '', '', true ],
        [ false, false, true ],
        [ true, true, true ],
        [ $class1, $class1, true ],
        [ $class1, $class2, false ],
        [ $class1, false, false ],
        [ $class1, $func1, false ],
        [ $func1, $func1, true ],
        [ $func1, $func2, false ],
        [ $func1, $class1, false ],
        [ [], [], true ],
        [ [ 'abc' => 1 ], [], false ],
        [ [], [ 'abc' => 1 ], false ],
        [ [ 'abc' => 1 ], [ 'abc' => 1 ], true ],
        [ [ 0 => 1 ], [], false ],
        [ [], [ 0 => 1 ], false ],
        [ [ 0 => 1 ], [ 0 => 1 ], true ],
    ];

  } // testDataProvider

} // NBD_Validation_Rules_MatchesRuleTest