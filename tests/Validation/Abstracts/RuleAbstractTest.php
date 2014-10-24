<?php
/**
 * @group validation
 */
class NBD_Validation_Abstracts_RuleAbstractTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Abstracts\RuleAbstract';

  /**
   * @test
   */
  public function getSetErrorMessage() {

    $class       = $this->getMockForAbstractClass( $this->_class );
    $message     = $class->getErrorTemplate();

    $new_message = 'Something failed validation';

    $class->setErrorTemplate( $new_message );

    $this->assertNotEquals( $message, $class->getErrorTemplate() );
    $this->assertEquals( $new_message, $class->getErrorTemplate() );

  } // getSetErrorMessage

} // NBD_Validation_Abstracts_RuleAbstractTest
