<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_ArrayOfRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = '\Behance\NBD\Validation\Rules\ArrayOfRule';

  /**
   * @test
   * @dataProvider nonArrayProvider
   *
   * @param mixed $parameter_value The non-array object that will fail validation
   */
  public function arrayOfNonArray( $parameter_value ) {

    $rule            = new $this->_class();
    $result          = $rule->isValid( $parameter_value, [] );

    $this->assertFalse( $result );

  } // arrayOfNonArray


  /**
   * @test
   * @dataProvider fullyMockedProvider
   *
   * @param array $parameter_value_map  A map of [ "single value" => "inner validator result" ] tuples
   * @param bool  $outcome              The overall result of the (outer) validator
   */
  public function mockedArrayOf( array $parameter_value_map, $outcome ) {

    // TODO: This was the original, fully-mocked test for "simple", non-recursive/non-parsed
    //  arrays, prior to the refactor. The general "fully-mocked" tests should be re-established
    //  and updated for the final implementation.  Keeping this test alive, but skipping, so it
    //  can be refactored.
    $this->markTestIncomplete();

    $parameter_value = array_keys( $parameter_value_map );

    $inner_rule_name = 'fakeRule';

    $inner_rule      = $this->getMock( '\Behance\NBD\Validation\Interfaces\RuleInterface' );
    $provider        = $this->getMock( '\Behance\NBD\Validation\Interfaces\RulesProviderInterface' );
    $validator       = $this->getMock( '\Behance\NBD\Validation\Interfaces\ValidatorServiceInterface' );

    $context         = [
        'parameters' => [ $inner_rule_name ],
        'validator'  => $validator,
        'field'      => 'my_field_name'
    ];

    $value_map = [];
    foreach ( $parameter_value as $data ) {
      $value_map[] = [ $data, $context, $parameter_value_map[ $data ] ];
    }

    $inner_rule->expects( $this->atLeastOnce() )
      ->method( 'isValid' )
      ->will( $this->returnValueMap( $value_map ) );


    $provider->expects( $this->once() )
      ->method( 'getRule' )
      ->with( $inner_rule_name )
      ->will( $this->returnValue( $inner_rule ) );


    $validator->expects( $this->once() )
      ->method( 'getRulesProvider' )
      ->will( $this->returnValue( $provider ) );

    $rule   = new $this->_class();
    $result = $rule->isValid( $parameter_value, $context );

    $this->assertEquals( $outcome, $result );

  } // mockedArrayOf


  /**
   * @return array
   */
  public function fullyMockedProvider() {

    return [
        'Array of three; all are valid' => [
            'params' => [
                1       => true,
                '2'     => true,
                'three' => true
            ],
            'outcome' => true
        ],
        'Array of three; all invalid' => [
            'params' => [
                1       => false,
                '2'     => false,
                'three' => false
            ],
            'outcome' => false
        ],
        'Array of three; second is invalid' => [
            'params' => [
                1       => true,
                '2'     => false,
                'three' => true
            ],
            'outcome' => false
        ],
//        'Array of nothing' => [
//            'params' => [],
//            'outcome' => false
//        ]
    ];

  } // fullyMockedProvider


  /**
   * @return array
   */
  public function nonArrayProvider() {

    return [
        'A class'  => [ new \stdClass() ],
        'An int'   => [ 3 ],
        'A string' => [ 'A string' ],
        'Null'     => [ null ]
    ];

  } // nonArrayProvider


  /**
   * @test
   * @dataProvider integrationArrayOfProvider
   */
  public function integrationArrayOf( $data, $rule, $expected, \Closure $custom = null ) {

    $fields = [
        'field_name' => $data
    ];

    $validator = new \Behance\NBD\Validation\Services\ValidatorService( $fields );

    if ( $custom ) {
      $provider = $validator->getRulesProvider();
      $provider->setCallbackRule( 'custom', $custom );
    }

    $validator->setRule( 'field_name', 'Field Name', $rule );

    $actual = $validator->run();

    $this->assertEquals( $expected, $actual );

  } // integrationArrayOf


  /**
   * @return array
   */
  public function integrationArrayOfProvider() {

    $custom = function( $data ) {
        return $data % 3 === 0;
    }; // custom validation closure

    return [
        [
            'data'    => [ 1, '2', 3 ],
            'rule'    => 'arrayOf[integer]',
            'outcome' => true
        ],
        [
            'data'    => [ 1, 2, 'testing' ],
            'rule'    => 'arrayOf[integer]',
            'outcome' => false
        ],
        [
            'data'    => [ 'testing1', 'testing2' ],
            'rule'    => 'arrayOf[minLength[5]]',
            'outcome' => true
        ],
        [
            'data'    => [ 'testing1', 'test' ],
            'rule'    => 'arrayOf[minLength[5]]',
            'outcome' => false
        ],
        [
            'data'    => [ 3, 6, 9, 12 ],
            'rule'    => 'arrayOf[custom]',
            'outcome' => true,
            'custom'  => $custom
        ],
        [
            'data'    => [ 3, 6, 8 ],
            'rule'    => 'arrayOf[custom]',
            'outcome' => false,
            'custom'  => $custom
        ],
        [
            'data'    => [ [ 3, 6 ], [ 9, 12, 15 ] ],
            'rule'    => 'arrayOf[arrayOf[custom]]',
            'outcome' => true,
            'custom'  => $custom
        ],
        [
            'data'    => [ [ 3, 6 ], [ 8 ], [ 9, 12, 15 ] ],
            'rule'    => 'arrayOf[arrayOf[custom]]',
            'outcome' => false,
            'custom'  => $custom
        ],
        [
            'data'    => [ [ 3, 6 ], 9, 12 ],
            'rule'    => 'arrayOf[arrayOf[custom]]',
            'outcome' => false,
            'custom'  => $custom
        ],
        [
            'data'    => [ [ 'a', 'b', 'c' ], [ 'd', 'e', 'f' ] ],
            'rule'    => 'arrayOf[arrayOf[alpha|maxLength[1]]]',
            'outcome' => true
        ],
        [
            'data'    => [ [ 'a', 'b', 'c' ], [ 'd', 'e', 'fg' ] ],
            'rule'    => 'arrayOf[arrayOf[alpha|maxLength[1]]]',
            'outcome' => false
        ],
        [
            'data'    => [ [ [ 1, 2 ], [ 3, '4', 5 ] ] ],
            'rule'    => 'arrayOf[arrayOf[arrayOf[required|integer]]]',
            'outcome' => true
        ],
        [
            'data'    => 'String',
            'rule'    => 'arrayOf[alpha]',
            'outcome' => false
        ],
        [
            'data'    => [ [ new \stdClass() ], [ new stdClass(), new \stdClass() ] ],
            'rule'    => 'arrayOf[arrayOf[instanceOf[\stdClass]]]',
            'outcome' =>  true
        ],
        [
            'data'    => [ new \stdClass(), 'stdClass', new \stdClass() ],
            'rule'    => 'arrayOf[instanceOf[stdClass]]',
            'outcome' =>  false
        ],
        [
            'data'    => [],
            'rule'    => 'arrayOf[alpha]',
            'outcome' => true
        ],
//        [
//            'data'    => [],
//            'rule'    => 'arrayOf[required|alpha]',
//            'outcome' => false
//        ]
    ];

  } // integrationArrayOfProvider


  // TODOs:
  // [ ]  arrayOf[] - no param count should throw exception
  // [ ]  arrayOf[required|alpha] - should fail if an element is null
  // [X]  arrayOf[alpha] - should not fail if an element is null
  // [ ]  maxLength[3]|arrayOf[alpha] - ensure count($array) <= $max  (<---- LATER)


} // NBD_Validation_Rules_ArrayOfRuleTest
