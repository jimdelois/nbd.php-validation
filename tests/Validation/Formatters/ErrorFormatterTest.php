<?php

use Behance\NBD\Validation\Formatters\ErrorFormatter;

/**
 * @group validation
 */
class NBD_Validation_Formatters_ErrorFormattersTest extends PHPUnit_Framework_TestCase {

  private $_class = 'Behance\NBD\Validation\Formatters\ErrorFormatter',
          $_rule  = 'Behance\NBD\Validation\Rules\IntegerRule';


  /**
   * @test
   */
  public function getterSetter() {

    $context = [ 'abc' => 123 ];
    $rule    = $this->getMock( $this->_rule, null );
    $class   = $this->getMock( $this->_class, null, [ $rule, $context ] );

    $this->assertSame( $rule, $class->getRule() );
    $this->assertEquals( $context, $class->getContext() );

  } // getterSetter


  /**
   * @test
   * @dataProvider renderProvider
   */
  public function renderContext( $template, array $context, $rendered, $constructor_context ) {

    $rule = $this->getMock( $this->_rule, null );
    $rule->setErrorTemplate( $template );

    $params = [ $rule ];

    if ( $constructor_context && !empty( $context ) ) {
      $params[] = $context;
    }

    $class = $this->getMock( $this->_class, null, $params );

    if ( $constructor_context ) {
      $this->assertEquals( $rendered, $class->render() );
    }
    else {
      $this->assertEquals( $rendered, $class->render( $context ) );
    }

  } // renderContext


  /**
   * @test
   * @dataProvider renderProvider
   */
  public function renderContextOverrides() {

    $value0 = ErrorFormatter::FIELDNAME_DEFAULT;
    $value1 = 'value1';
    $value2 = 'value2';

    $constructor_context = [ 'fieldname' => $value1 ];
    $render_context      = [ 'fieldname' => $value2 ];

    $template  = '%fieldname% had a problem';

    $rendered0 = "{$value0} had a problem";
    $rendered1 = "{$value1} had a problem";
    $rendered2 = "{$value2} had a problem";

    $rule = $this->getMock( $this->_rule, null );
    $rule->setErrorTemplate( $template );

    $default = $this->getMock( $this->_class, null, [ $rule ] );
    $class   = $this->getMock( $this->_class, null, [ $rule, $constructor_context ] );

    // Default context takes precedence
    $this->assertEquals( $rendered0, $default->render() );

    // Constructor context takes precendence
    $this->assertEquals( $rendered1, $class->render() );

    // Render context takes precedence
    $this->assertEquals( $rendered2, $class->render( $render_context ) );

  } // renderContextOverrides


  /**
   * @return array
   */
  public function renderProvider() {

    $key     = 'abc';
    $value   = 'anything';

    $message = "This is a message about %key% with %value%";
    $partial = "This is a message about {$key} with %value%";
    $full    = "This is a message about {$key} with {$value}";

    return [
        'Unrendered'     => [ $message, [], $message, false ],
        'Unrendered'     => [ $message, [], $message, true ],
        'Partial render' => [ $message, [ 'key' => $key ], $partial, true ],
        'Partial render' => [ $message, [ 'key' => $key ], $partial, true ],
        'Full render'    => [ $message, [ 'key' => $key, 'value' => $value ], $full, false ],
        'Full render'    => [ $message, [ 'key' => $key, 'value' => $value ], $full, true ],
        'Full render with non-string' => [ $message, [ 'key' => $key, 'value' => $value, 'object' => ( function() {} ) ], $full, true ],
    ];

  } // renderProvider

} // NBD_Validation_Formatters_ErrorFormattersTest
