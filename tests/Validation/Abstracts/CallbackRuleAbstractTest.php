<?php
/**
 * @group validation
 */
class NBD_Validation_Abstracts_CallbackRuleAbstractTest extends PHPUnit_Framework_TestCase {

  protected $_class = 'Behance\NBD\Validation\Abstracts\CallbackRuleAbstract';

  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function getClosureNotSet() {

    $class = $this->getMockForAbstractClass( $this->_class );

    $class->getClosure();

  } // getClosureNotSet

} // NBD_Validation_Abstracts_CallbackRuleAbstractTest
