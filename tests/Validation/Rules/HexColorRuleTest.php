<?php

namespace Behance\NBD\Validation\Rules;

/**
 * @group validation
 */
class HexColorRuleTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   * @dataProvider testDataProvider
   */
  public function isValid( $data, $expected ) {

    $rule = new HexColorRule();

    $this->assertEquals( $expected, $rule->isValid( $data ) );

  } // isValid


  /**
   * @return array
   */
  public function testDataProvider() {

    return [
        [ 'abc', true ],
        [ 'ABC', true ],
        [ 'aBc000', true ],
        [ 'abc000', true ],
        [ 'ABC000', true ],
        [ 'A000AF', true ],
        [ '123456', true ],
        [ '1234567', false ],
        [ '123456 ', false ],
        [ '#123456 ', false ],
        [ ' 123456 ', false ],
        [ ' 123456', false ],
        [ '12345', false ],
        [ 'A000AG', false ],
        [ 'ábč', false ],
        [ 'ábčabc', false ],
        [ 'ÁBČabc', false ],
        [ 123.0e26, false ],
        [ null, false ],
        [ false, false ],
        [ true, false ],
        [ ( new \stdClass() ), false ],
        [ ( function() {} ), false ],
    ];

  } // testDataProvider

} // HexColorRuleTest
