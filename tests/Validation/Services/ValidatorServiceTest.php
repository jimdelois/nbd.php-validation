<?php

use Behance\NBD\Validation\Services\ValidatorService;
use Behance\NBD\Validation\Providers\RulesProvider;

/**
 * @group validation
 */
class NBD_Validation_Services_ValidatorServiceTest extends PHPUnit_Framework_TestCase {

  private $_target = 'Behance\NBD\Validation\Services\ValidatorService';


  /**
   * @test
   */
  public function setGetRulesProvider() {

    $validator = new ValidatorService();
    $rules     = new RulesProvider();

    $this->assertNotSame( $rules, $validator->getRulesProvider() );

    $validator->setRulesProvider( $rules );

    $this->assertSame( $rules, $validator->getRulesProvider() );

  } // setGetRulesProvider


  /**
   * @test
   */
  public function setGetRulesProviderConstructor() {

    $rules     = $this->getMock( 'Behance\NBD\Validation\Interfaces\RulesProviderInterface' );
    $validator = new ValidatorService( [], $rules );

    $this->assertSame( $rules, $validator->getRulesProvider() );

  } // setGetRulesProviderConstructor


  /**
   * @test
   */
  public function setGetRule() {

    $key       = 'email';
    $fieldname = 'E-Mail';
    $rules     = 'required|email';
    $validator = new ValidatorService();

    $exploded_rules = explode( '|', $rules );

    // Also assert the fluent interface
    $this->assertEquals( $validator, $validator->setRule( $key, $fieldname, $rules ) );

    $this->assertEquals( $fieldname, $validator->getFieldName( $key ) );
    $this->assertEquals( $exploded_rules, $validator->getFieldRules( $key ) );

  } // setGetRule


  /**
   * Plural convenience method of above test
   *
   * @test
   */
  public function setGetRules() {

    $validator  = new ValidatorService();
    $key        = 'email';
    $fieldname  = 'E-Mail';
    $data_rules = 'required|email';
    $rules      = [
        [ $key, $fieldname, $data_rules ]
    ];

    $exploded_rules = explode( '|', $data_rules );

    // Also assert the fluent interface
    $this->assertEquals( $validator, $validator->setRules( $rules ) );

    $this->assertEquals( $fieldname, $validator->getFieldName( $key ) );
    $this->assertEquals( $exploded_rules, $validator->getFieldRules( $key ) );

  } // setGetRules


  /**
   * @test
   */
  public function setGetCageData() {

    $validator = new ValidatorService();

    $this->assertEmpty( $validator->getCageData() );

    $data = [ 'key' => 'value', 'abc' => 123 ];

    $validator->setCageData( $data );

    $this->assertEquals( $data, $validator->getCageData() );

  } // setGetCageData


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function getRulesUnknownKey() {

    ( new ValidatorService() )->getFieldRules( 'badkey' );

  } // getRulesUnknownKey


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function setRulesMissingArgs() {

    $validator = new ValidatorService();
    $rules[]   = [ 'email', 'E-Mail' ];

    $validator->setRules( $rules );

  } // setRulesMissingArgs


  /**
   * @test
   */
  public function appendRule() {

    $validator  = new ValidatorService();
    $key  = 'email';
    $rule = 'some_kinda_rule';

    $validator->setRule( 'email', 'E-Mail', 'required|isEmail' );

    // Also assert fluent interface
    $this->assertEquals( $validator, $validator->appendRule( $key, $rule ) );

    $this->assertContains( $rule, $validator->getFieldRules( $key ) );

  } // appendRule


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function appendRuleUnsetKey() {

    ( new ValidatorService() )->appendRule( 'nonExistentKeyGoesHere', 'somethingSomethingRule' );

  } // appendRuleUnsetKey


  /**
   * @test
   */
  public function getValidDataEmpty() {

    $this->assertEmpty( ( new ValidatorService() )->getValidatedData() );

  } // getValidDataEmpty


  /**
   * @test
   */
  public function getValidatedData() {

    $data      = [ 'new_password' => 'something' ];
    $validator = new ValidatorService( $data );

    $validator->setRule( 'new_password', 'New Password', 'required|minLength[1]|maxLength[10]' );

    $this->assertTrue( $validator->run() );

    $this->assertEquals( $data, $validator->getValidatedData() );

  } // getValidatedData


  /**
   * @test
   */
  public function getValidDataStripTest() {

    $data = [ 'new_password' => 'something' ];

    $validator = new ValidatorService( $data );

    $validator->setRule( 'email', 'E-mail', 'required|minLength[1]|maxLength[10]' );

    $this->assertFalse( $validator->run() );

    $this->assertEquals( [], $validator->getValidatedData( true ) );

  } // getValidDataStripTest


  /**
   * @test
   */
  public function getValidDataFalse() {

    $validator = new ValidatorService();

    $validator->setRule( 'new_password', 'New Password', 'required|minLength[1]|maxLength[10]' );

    $this->assertFalse( $validator->run() );
    $this->assertEquals( [], $validator->getValidatedData() );

  } // getValidDataFalse


  /**
   * @test
   */
  public function getValidatedFields() {

    $data = [ 'new_password' => 'something' ];

    $validator = new ValidatorService( $data );

    $validator->setRule( 'new_password', 'New Password', 'required|minLength[1]|maxLength[10]' )
              ->run();

    $this->assertContains( 'new_password', $validator->getValidatedFields() );

  } // getValidatedFields


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\NotRunException
   */
  public function getValidatedFieldsNotRun() {

    $key  = 'password';
    $data = [ $key => 'something' ];

    $validator = new ValidatorService( $data );

    $validator->setRule( $key, 'New Password', 'required|minLength[1]|maxLength[10]' );

    $validator->getValidatedField( $key );

  } // getValidatedFieldsNotRun


  /**
   * @test
   */
  public function getFieldNameEmpty() {

    $validator = new ValidatorService();

    $this->assertEquals( '', $validator->getFieldName( 'something-not-set' ) );

  } // getFieldNameEmpty


  /**
   * @test
   */
  public function getFieldName() {

    $validator  = new ValidatorService();
    $key        = 'email';
    $field_name = 'E-Mail';

    $validator->setRule( $key, $field_name, 'required|isEmail' );
    $this->assertEquals( $field_name, $validator->getFieldName( $key ) );

  } // getFieldName


  /**
   * @test
   */
  public function getFields() {

    $validator = new ValidatorService();
    $key1      = 'email';
    $key2      = 'url';

    $validator->setRule( $key1,  'E-Mail', 'required|isEmail' )
              ->setRule( $key2, 'URL',    'required|isUrl' );

    $this->assertEquals( [ $key1, $key2 ], $validator->getFields() );

  } // getFields


  /**
   * @test
   */
  public function isFieldRequiredFalse() {

    $validator = new ValidatorService();
    $key       = 'email';

    $validator->setRule( $key, 'E-Mail', 'integer' );

    $this->assertFalse( $validator->isFieldRequired( $key ) );

  } // isFieldRequiredFalse


  /**
   * @test
   */
  public function isFieldRequiredTrue() {

    $validator = new ValidatorService();
    $key       = 'email';

    $validator->setRule( $key, 'E-Mail', 'required|isEmail' );

    $this->assertTrue( $validator->isFieldRequired( $key ) );

  } // isFieldRequiredTrue


  /**
   * @test
   */
  public function setGetMessageDelimiter() {

    $delimiter = ';';
    $validator = new ValidatorService();

    // Ensure fluent interface exists
    $this->assertSame( $validator, $validator->setMessageDelimiter( $delimiter ) );

    $this->assertSame( $delimiter, $validator->getMessageDelimiter() );

  } // setGetMessageDelimiter


  /**
   * @test
   */
  public function getFailedFields() {

    $validator = new ValidatorService();
    $key       = 'email';
    $message   = 'a message goes here';

    $validator->setRule( $key, '', '' )
              ->addFieldFailure( $key, $message );

    $this->assertEquals( [ $key ], $validator->getFailedFields() );

  } // getFailedFields


  /**
   * @test
   */
  public function getFailedFieldsEmpty() {

    $validator = new ValidatorService();

    $this->assertEmpty( $validator->getFailedFields() );

  } // getFailedFieldsEmpty


  /**
   * @test
   */
  public function isFieldFailedFalse() {

    $validator = new ValidatorService();
    $key       = 'email';
    $message   = 'a message goes here';
    $rules[]   = [ 'email', '', '' ];

    $validator->setRules( $rules )
              ->addFieldFailure( $key, $message );

    $this->assertFalse( $validator->isFieldFailed( 'nothing' ) );

  } // isFieldFailedFalse


  /**
   * @test
   */
  public function isFieldFailedTrue() {

    $validator = new ValidatorService();
    $key       = 'email';
    $message   = 'a message goes here';

    $validator->setRule( $key, '', '' )
              ->addFieldFailure( $key, $message );

    $this->assertTrue( $validator->isFieldFailed( $key ) );

  } // isFieldFailedTrue


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function addErrorWrongKey() {

    $validator = new ValidatorService();
    $key       = 'email';
    $message   = 'a message goes here';

    $validator->addFieldFailure( $key, $message );

  } // addErrorWrongKey


  /**
   * @test
   */
  public function addFieldFailure() {

    $validator = new ValidatorService();
    $key       = 'email';
    $message   = 'this thing failed validation';

    $validator->setRule( $key, 'E-Mail', 'required|isEmail' )
              ->addFieldFailure( $key, $message );

    $error_array = $validator->getAllFieldErrorMessages();

    $this->assertArrayHasKey( $key, $error_array );
    $this->assertEquals( $message, $error_array[ $key ] );

    $this->assertContains( $message, $validator->getAllFieldErrorMessagesString() );

  } // addFieldFailure


  /**
   * @test
   * @expectedException BadMethodCallException
   */
  public function magicSetterDisabled() {

    $crv = new ValidatorService();

    $crv->test = 'uh-oh';

  } // magicSetterDisabled


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function magicGetUnavailable() {

    $crv  = new ValidatorService();

    $crv->field_names;

  } // magicGetUnavailable


  /**
   * @test
   */
  public function magicGetInvalidData() {

    $validator = new ValidatorService();
    $key       = 'email';

    $validator->setRule( $key, 'E-Mail', 'required|isEmail' );

    $this->assertFalse( $validator->run() );
    $this->assertTrue( $validator->isFieldFailed( $key ) );

    $this->assertEquals( null, $validator->$key ) ;

  } // magicGetInvalidData


  /**
   * @test
   */
  public function getErrorsByKey() {

    $validator = new ValidatorService();
    $key       = 'email';
    $name      = 'E-Mail';

    $message   = '%fieldname% was not good enough';
    $expected  = "{$name} was not good enough";


    $validator->setRule( $key, $name, 'required|email' )
      ->addFieldFailure( $key, $message );

    $this->assertEquals( $expected, $validator->getFieldErrorMessage( $key ) );

  } // getErrorsByKey


  /**
   * @test
   */
  public function runStrictGood() {

    $key       = 'abc';
    $value     = 'def';
    $validator = new ValidatorService( [ $key => $value ] );
    $validator->setRule( $key, 'ABC', "required|stringContains[{$value}]" );

    $this->assertTrue( $validator->runStrict() );

    $this->assertEquals( '', $validator->getFieldErrorMessage( $key ) );

  } // runStrictGood


  /**
   * @test
   */
  public function runStrictBad() {

    $key       = 'abc';
    $value     = 123;
    $validator = new ValidatorService( [ $key => $value ] );

    $validator->setRule( $key, 'ABC', "required|alpha" );

    try {
      $validator->runStrict();
      $this->fail( "Should have thrown exception" );
    }

    catch( Behance\NBD\Validation\Exceptions\Validator\FailureException $e ) {
      $this->assertSame( $validator, $e->getValidator() );
    }

  } // runStrictBad


  /**
   * @test
   * @expectedException  Behance\NBD\Validation\Exceptions\Validator\NotRunException
   */
  public function runNoRules() {

    $test = new ValidatorService();

    $test->run();

  } // runNoRules


  /**
   * @test
   * @expectedException  Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runOnlyRequiredError() {

    $test = new ValidatorService();

    $test->setRule( 'email', 'E-Mail', 'required' );
    $test->run();

  } // runOnlyRequiredError


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException
   */
  public function runInvalidRule() {

    $test = new ValidatorService( [ 'email' => 'an_email_goes_here' ] );

    $test->setRule( 'email', 'E-Mail', 'required|isBlah2' );

    $test->run();

  } // runInvalidRule


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException
   */
  public function runInvalidParameterizedRule() {

    $test = new ValidatorService( [ 'email' => 'an_email_goes_here' ] );

    $test->setRule( 'email', 'E-Mail', 'required|isBlah2[arg1,arg2]' );

    $test->run();

  } // runInvalidParameterizedRule


  /**
   * @test
   */
  public function runFailedFieldValidation() {

    $bad_key  = 'bad_comment';
    $good_key = 'comment';
    $data     = [
        $bad_key  => '<alert>The number 3</alert>',
        $good_key => '<h1>Heres a valid-enough comment</h1>'
    ];

    $validator = new ValidatorService( $data );

    $validator->setRule( $bad_key,  'Bad comment',  'required|integer' )
              ->setRule( $good_key, 'Good Comment', 'required|maxLength[800]' );

    $this->assertFalse( $validator->run() );
    $this->assertTrue( $validator->isFieldFailed( $bad_key ) );
    $this->assertFalse( $validator->isFieldFailed( $good_key ) );

    $messages      = $validator->getAllFieldErrorMessages();
    $failed_fields = $validator->getFailedFields();

    $this->assertArrayHasKey( $bad_key, $messages );
    $this->assertContains( $bad_key, $failed_fields );

    $this->assertArrayNotHasKey( $good_key, $messages );
    $this->assertArrayNotHasKey( $good_key, $failed_fields );

  } // runFailedFieldValidation


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Rules\UnknownRuleException
   */
  public function runNonFunction() {

    $key       = 'abc';
    $validator = new ValidatorService( [ $key => 'asdfdas' ] );

    $validator->setRule( $key, 'Key', 'required|not_a_function' );
    $validator->run();

  } // runNonFunction


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function runCallbackNonExistantClass() {

    $key       = 'abc';
    $validator = new ValidatorService( [ $key => 12345 ] );

    $validator->setRule( $key, 'Key', 'callback[Derp,ok]' );

    $validator->run();

  } // runCallbackNonExistantClass


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runCallbackWrongParams() {

    $key       = 'abc';
    $validator = new ValidatorService( [ $key => 12345 ] );

    $validator->setRule( $key, 'Key', 'callback[Derp]' );

    $validator->run();

  } // runCallbackWrongParams


  /**
   * @test
   */
  public function runClosureConversion() {

    $key      = 'sha';
    $value    = 'rvoweicoewicjwoi102931029380921oimi1m3';
    $function = ( function( $data ) use ( $value ) {
      return ( $data == $value );
    } );

    $hash      = spl_object_hash( $function );
    $validator = new ValidatorService( [ $key => $value ] );

    $validator->setRule( $key, 'Commit SHA', [ 'required', 'maxLength[800]', 'alphaNumeric', $function ] );

    $this->assertTrue( $validator->run() );

    $rules     = $validator->getRulesProvider();
    $container = $rules->getRule( $hash );

    $this->assertSame( $function, $container->getClosure() );

  } // runClosureConversion


  /**
   * @return array
   */
  public function maximumStringProvider() {

    return [
        [ 5, '',             true ],
        [ 5, 'abcd',         true ],
        [ 5, 'abcde',        true ],
        [ 5, 'abcdef',       false ],
        [ 5, 'abcdefghijkl', false ],
        [ 5, true,           false ], // Will all fail for not being strings
        [ 5, false,          false ],
        [ 5, 5,              false ],
        [ 5, -5,             false ],
        [ 5, 0,              false ],
    ];

  } // maximumStringProvider


  /**
   * @test
   * @dataProvider maximumStringProvider
   */
  public function runMaximum( $max, $value, $expected ) {

    $name      = 'Commit SHA';
    $field     = 'sha';
    $validator = new ValidatorService( [ $field => $value ] );

    $validator->setRule( $field, $name, "required|maxLength[{$max}]" );

    $this->assertEquals( $expected, $validator->run() );

    if ( !$expected ) {

      $errors = $validator->getAllFieldErrorMessages();

      // Ensure both template variables were replaced
      $this->assertArrayHasKey( $field, $errors );
      $this->assertContains( (string)$max, $errors[ $field ] );
      $this->assertContains( $name, $errors[ $field ] );

    } // if !expected


  } // runMaximum


  /**
   * @return array
   */
  public function minimumStringProvider() {

    return [
        [ 5, '',             false ],
        [ 5, 'abcd',         false ],
        [ 5, 'abcde',        true ],
        [ 5, 'abcdef',       true ],
        [ 5, 'abcdefghijkl', true ],
        [ 5, true,           false ],
        [ 5, false,          false ],
        [ 5, 5,              false ],
        [ 5, -5,             false ],
        [ 5, 0,              false ],
    ];

  } // minimumStringProvider


  /**
   * @test
   * @dataProvider minimumStringProvider
   */
  public function runMinimum( $min, $value, $expected ) {

    $field     = 'title';
    $name      = 'Field Title';
    $validator = new ValidatorService( [ $field => $value ] );
    $validator->setRule( $field, $name, "required|minLength[{$min}]" );

    $this->assertEquals( $expected, $validator->run() );

    if ( !$expected ) {

      $errors = $validator->getAllFieldErrorMessages();

      // Ensure both template variables were replaced
      $this->assertArrayHasKey( $field, $errors );
      $this->assertContains( (string)$min, $errors[ $field ] );
      $this->assertContains( $name, $errors[ $field ] );

    } // if !expected

  } // runMinimum


  /**
   * @test
   */
  public function runMatches() {

    $key1  = 'new_password';
    $key2  = 'confirm_password';
    $value = 'heresthealphapassword'; // Ensure both fields are populated with the same value
    $data  = [
        $key1  => $value,
        $key2  => $value
    ];

    $validator = new ValidatorService( $data );

    $validator->setRule( $key1, 'New Password',     'required|alpha' )
              ->setRule( $key2, 'Confirm Password', "required|alpha|matches[{$key1}]" );

    $this->assertTrue( $validator->run() );

  } // runMatches


  /**
   * @test
   */
  public function runNonMatches() {

    $key1 = 'new_password';
    $key2 = 'confirm_password';

    $data = [
        $key1 => 'Password',
        $key2 => 'Passworder'
    ];

    $validator = new ValidatorService( $data );

    $validator->setRule( $key1, 'New Password',     'required|alpha' )
              ->setRule( $key2, 'Confirm Password', 'required|alpha|matches[' . $key1 . ']' );

    $this->assertFalse( $validator->run() );

  } // runNonMatches


  /**
   * @test
   */
  public function runFilter() {

    $value     = 'password';
    $data      = [ 'new_password' => $value ];
    $validator = new ValidatorService( $data );

    $validator->setRule( 'new_password', 'New Password', 'required|filter[md5]' );

    $this->assertTrue( $validator->run() );

    $this->assertEquals( md5( $value ), $validator->new_password );

  } // runFilter


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException
   */
  public function runFilterBadFunction() {

    $key          = 'new_password';
    $data         = [ $key => 'password' ];
    $validator    = new ValidatorService( $data );
    $bad_function = 'not_a_function';

    $this->assertFalse( function_exists( $bad_function ) );

    $validator->setRule( $key, 'New Password', 'required|filter[' . $bad_function . ']' );
    $validator->run();

  } // runFilterBadFunction


  /**
   * @test
   */
  public function runFilterChain() {

    $value = '      apple   ';
    $data  = [ 'new_password'  => $value ];

    $validator = new ValidatorService( $data );

    $validator->setRule( 'new_password', 'New Password', 'required|filter[trim,sha1,md5]' );

    $this->assertTrue( $validator->run() );

    // IMPORTANT: filter chain operates left to right as defined
    $expected = md5( sha1( trim( $value ) ) );

    $this->assertEquals( $expected, $validator->new_password  );

  } // runFilterChain


  /**
   * @test
   * @expectedException Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runFilterMissingTrailingBracket() {

    $key       = 'abc';
    $validator = new ValidatorService( [ $key => 'asdasda' ] );

    $validator->setRule( $key, 'Key', 'required|filter[md5' );
    $validator->run();

  } // runFilterMissingTrailingBracket


  /**
   * @test
   */
  public function runRangeGood() {

    $key   = 'ranged_key';
    $min   = 1;
    $max   = 10;

    $validator = new ValidatorService();
    $validator->setRule( $key, 'Ranged Key', "required|range[{$min},{$max}]" );

    for ( $ix = $min; $ix <= $max; ++$ix ) {

      $validator->setCageData( [ $key => $ix ] );

      $this->assertTrue( $validator->run() );

      $this->assertEquals( $ix, $validator->$key );

    } // for ix

  } // runRangeGood


  /**
   * @test
   */
  public function runRangeBad() {

    $key   = 'ranged_key';
    $min   = 1;
    $max   = 10;

    $validator = new ValidatorService();
    $validator->setRule( $key, 'Ranged Key', "required|range[{$min},{$max}]|integer" );

    for ( $ix = ( $min - 5 ); $ix < $min; ++$ix ) {

      $validator->setCageData( [ $key => $ix ] );

      $this->assertFalse( $validator->run() );

    } // for ix

    for ( $ix = ( $max + 1 ); $ix <= ( $max + 5 ); ++$ix ) {

      $validator->setCageData( [ $key => $ix ] );

      $this->assertFalse( $validator->run() );

    } // for ix

  } // runRangeBad


  /**
   * @test
   * @expectedException  Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runRangeWrongParamForRange() {

    $key       = 'ranged_key';
    $validator = new ValidatorService();
    $min       = 11;
    $max       = 10;

    $validator->setRule( $key, 'Ranged Key', "required|range[{$min},{$max}]" );

    $this->assertTrue( $min >= $max );

    $validator->setCageData( [ $key => 5 ] );

    $validator->run();

  } // runRangeWrongParamForRange


  /**
   * @test
   * @expectedException  Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runRangeWrongParams() {

    $key       = 'ranged_key';
    $validator = new ValidatorService();

    $validator->setRule( $key, 'Ranged Key', "required|range[1,2,3,4,5]" );

    $validator->setCageData( [ $key => 5 ] );

    $validator->run();

  } // runRangeWrongParams

  /**
   * @test
   */
  public function runContainsGood() {

    $key    = 'contains';
    $values = [ 'abc', 'def', 'ghi', 'jkl' ];

    $imploded  = implode( ',', $values );
    $validator = new ValidatorService();

    $validator->setRule( $key, 'Contains', "required|containedIn[{$imploded}]" );

    foreach ( $values as $value ) {

      $validator->setCageData( [ $key => $value ] );

      $this->assertTrue( $validator->run() );

      $this->assertEquals( $value, $validator->$key );

    } // foreach values

  } // runContainsGood


  /**
   * @test
   */
  public function runContainsBad() {

    $key         = 'contains';
    $good_values = [ 'abc', 'def', 'ghi', 'jkl' ];
    $bad_values  = [ 123, 456, 789, 012 ];

    $imploded  = implode( ',', $good_values );
    $validator = new ValidatorService();

    $validator->setRule( $key, 'Contains', "required|containedIn[{$imploded}]" );

    foreach ( $bad_values as $value ) {

      $validator->setCageData( [ $key => $value ] );

      $this->assertFalse( $validator->run() );

    } // foreach values

  } // runContainsBad


  /**
   * @test
   */
  public function runStringContainsGood() {

    $key       = 'string_contains';
    $values    = [ 'a', 'ab', 'abc', 'abcd', 'b', 'bc', 'bcd', 'c', 'cd', 'd' ];
    $haystack  = 'abcd';

    foreach ( $values as $needle ) {

      $validator = new ValidatorService();

      $validator->setCageData( [ $key => $needle ] );
      $validator->setRule( $key, 'String Contains', "required|stringContains[{$haystack}]" );

      $this->assertContains( $needle, $haystack );

      $this->assertTrue( $validator->run() );

    } // foreach values

  } // runStringContainsGood


  /**
   * @test
   */
  public function runStringContainsBad() {

    $key       = 'string_contains';
    $values    = [ 'a', 'ab', 'abc', 'b', 'bc', 'bcd', 'c', 'cd', 'd', 1, 12, 123, 1234, 12345 ];
    $haystack  = 'abcd';


    foreach ( $values as $needle ) {

      $validator = new ValidatorService();

      // IMPORTANT: Rule is consistent, data is changing -- opposite from stringContainsGood test
      $validator->setCageData( [ $key => $haystack ] );
      $validator->setRule( $key, 'String Contains', "required|stringContains[{$needle}]" );

      $this->assertNotContains( $haystack, (string)$needle );

      $this->assertFalse( $validator->run() );

      $this->assertTrue( $validator->isFieldFailed( $key ) );

    } // foreach values

  } // runStringContainsBad


  /**
   * @test
   * @expectedException  Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runStringContainsWrongParams() {

    $key       = 'string_contains_key';
    $validator = new ValidatorService();

    $validator->setRule( $key, 'String Contains', "required|stringContains[abcdef,ghijkl]" );

    $validator->setCageData( [ $key => 'abc' ] );

    $validator->run();

  } // runStringContainsWrongParams


  /**
   * @test
   */
  public function runInstanceOfGood() {

    $key        = 'instance_of';
    $validator  = new ValidatorService();
    $class_name = get_class( $this );
    $values     = [ $this, new $class_name(), $this->getMock( $class_name ) ];

    $validator->setRule( $key, 'Instance Of', "required|instanceOf[{$class_name}]" );

    foreach ( $values as $value ) {

      $this->assertInstanceOf( $class_name, $value );

      $validator->setCageData( [ $key => $value ] );

      $this->assertTrue( $validator->run() );
      $this->assertFalse( $validator->isFieldFailed( $key ) );

    } // foreach values

  } // runInstanceOfGood


  /**
   * @test
   */
  public function runInstanceOfBad() {

    $key        = 'instance_of';
    $class_name = get_class( $this );
    $bad_values = [ 12345, 67890, '12345', 'abcdef', new SplQueue(), new ArrayObject(), new SplStack(), new stdClass() ];

    foreach ( $bad_values as $value ) {

      $validator  = new ValidatorService();
      $validator->setRule( $key, 'Instance Of', "required|instanceOf[{$class_name}]" );

      $this->assertNotInstanceOf( $class_name, $value );

      $validator->setCageData( [ $key => $value ] );

      $this->assertFalse( $validator->run() );

      $this->assertTrue( $validator->isFieldFailed( $key ) );

    } // foreach values

  } // runInstanceOfBad


  /**
   * @test
   * @expectedException  Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException
   */
  public function runInstanceOfWrongParams() {

    $key       = 'instance_of_key';
    $validator = new ValidatorService();

    $validator->setRule( $key, 'Instance Of', "required|instanceOf[abcdef,ghijkl]" );

    $validator->setCageData( [ $key => $validator ] );

    $validator->run();

  } // runInstanceOfWrongParams


  /**
   * @test
   */
  public function runCallbackReturnTrue() {

    $class     = __CLASS__;
    $data      = [ 'number' => 6 ];
    $validator = new ValidatorService( $data );

    $validator->setRule( 'number', 'Number', "required|integer|callback[{$class},callbackIsIntPositive]" );
    $this->assertTrue( $validator->run() );

  } // runCallbackReturnTrue


  /**
   * @test
   */
  public function runCallbackReturnFalse() {

    $class  = __CLASS__;
    $data   = [ 'number'  => -1 ];
    $validator = new ValidatorService( $data );

    $validator->setRule( 'number', 'Number', "required|integer|callback[{$class},callbackIsIntPositive]" );

    $this->assertFalse( $validator->run() );

  } // runCallbackReturnFalse


  /**
   * @test
   */
  public function runCallbackProcessDefaultError() {

    $class     = __CLASS__;
    $key       = 'number';
    $name      = 'Number';
    $name      = 'callbackFailNoErrorAdded'; // Defined below
    $rule      = 'callback';

    $validator = $this->getMock( $this->_target, null );

    $validator->setCageData( [ $key  => -1 ] );

    $validator->setRule( $key, $name, "required|integer|{$rule}[{$class},{$name}]" );
    $this->assertFalse( $validator->run() );

    $this->assertStringStartsWith( $name, $validator->getFieldErrorMessage( $key ) );

  } // runCallbackProcessDefaultError


  /**
   * @return bool
   */
  public static function callbackIsIntPositive( $data ) {

    return ( is_int( $data ) && $data > 0 );

  } // callbackIsIntPositive


  /**
   * Use function as stand-in for callback function, that does not set an error back when failing
   *
   * @return bool (false only)
   */
  public static function callbackFailNoErrorAdded( $data ) {

    // Only to satisfy PHPMD
    $data;

    return false;

  } // callbackFailNoErrorAdded


} // NBD_Validation_Services_ValidatorServiceTest
