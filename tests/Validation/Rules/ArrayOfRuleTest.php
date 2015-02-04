<?php
/**
 * @group validation
 */
class NBD_Validation_Rules_ArrayOfRuleTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Rules\ArrayOfRule';

  /**
   * @test
   */
  public function isValid() {

//    $provider = new \Behance\NBD\Validation\Providers\RulesProvider();

//    $name = $this->_class;
//    $rule = new $name( );

    $data = [
        'thing' => [ 1, 2, '3' ]
    ];

    $validator = new \Behance\NBD\Validation\Services\ValidatorService( $data );
    $validator->setRule( 'thing', 'A thing', 'arrayOf[integer]' );

    $result = $validator->run();
    $this->assertTrue( $result );



    $data = [
        'another_thing' => [ [ 1, 2, 3 ], [ 4, 5, 6 ] ]
    ];

    $validator = new \Behance\NBD\Validation\Services\ValidatorService( $data );
    $validator->setRule( 'another_thing', 'A thing', 'arrayOf[arrayOf[integer]]' );

    $result = $validator->run();

    $this->assertTrue( $result );







    $data = [
        'ridiculous_structure' => [
            [
                [ 'a', 'b', 'c' ],
                [ 'd', 'e', 'f', 'g' ]
            ],
            [
                [ 'h', 'i', 'j' ],
                [ 'k', 'l', 'm', 'n', 'o', 'p' ]
            ]
        ]
    ];

    $validator = new \Behance\NBD\Validation\Services\ValidatorService( $data );
    $validator->setRule( 'ridiculous_structure', 'A thing', 'required|arrayOf[arrayOf[arrayOf[alpha]]]' );

    $result = $validator->run();

    $this->assertTrue( $result );


//    $defn = 'arrayOf[arrayOf[integer]]';



  } // isValid

} // NBD_Validation_Rules_ArrayOfRuleTest
