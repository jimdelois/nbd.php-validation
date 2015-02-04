<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_ArrayOfRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\ArrayOfRule';

  /**
   * @test
   * @dataProvider nonArrayProvider
   *
   * @param mixed $parameter_value The non-array object that will fail validation
   */
  public function mockedArrayOfNonArray( $parameter_value ) {

    $rule            = new $this->_class();
    $result          = $rule->isValid( $parameter_value, [] );

    $this->assertFalse( $result );

  } // mockedArrayOfNonArray


  /**
   * @test
   * @dataProvider fullyMockedProvider
   *
   * @param array $parameter_value_map  A map of [ "single value" => "inner validator result" ] tuples
   * @param bool  $outcome              The overall result of the (outer) validator
   */
  public function mockedArrayOf( array $parameter_value_map, $outcome ) {

    $parameter_value = array_keys( $parameter_value_map );

    $inner_rule_name = 'fakeRule';

    $inner_rule      = $this->getMock( 'Behance\NBD\Validation\Interfaces\RuleInterface' );
    $provider        = $this->getMock( 'Behance\NBD\Validation\Interfaces\RulesProviderInterface' );
    $validator       = $this->getMock( 'Behance\NBD\Validation\Interfaces\ValidatorServiceInterface' );

    $context         = [
        'parameters' => [ $inner_rule_name ],
        'validator'  => $validator
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
//        [
//            'data'    => [ 'testing1', 'testing2' ],
//            'rule'    => 'arrayOf[minLength[5]]',
//            'outcome' => true
//        ],
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
        ]
    ];

  } // integrationArrayOfProvider

} // NBD_Validation_Rules_ArrayOfRuleTest
