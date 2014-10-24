<?php

namespace Behance\NBD\Validation\Services;

use Behance\NBD\Validation\Interfaces\ValidatorServiceInterface;
use Behance\NBD\Validation\Interfaces\RulesProviderInterface;
use Behance\NBD\Validation\Interfaces\RuleInterface;

use Behance\NBD\Validation\Providers\RulesProvider;
use Behance\NBD\Validation\Formatters\ErrorFormatter;
use Behance\NBD\Validation\Rules\Templates\CallbackTemplateRule;

use Behance\NBD\Validation\Exceptions\Validator\InvalidRuleException;
use Behance\NBD\Validation\Exceptions\Validator\RuleRequirementException;
use Behance\NBD\Validation\Exceptions\Validator\FailureException;
use Behance\NBD\Validation\Exceptions\Validator\NotRunException;
use Behance\NBD\Validation\Exceptions\Validator\DuplicateRunException;
use Behance\NBD\Validation\Exceptions\Validator\CallbackResultException;

class ValidatorService implements ValidatorServiceInterface {

  // Define special-case rules that need to be dealt with differently than standard ones
  const RULE_REQUIRED      = 'required';
  const RULE_MATCHES       = 'matches';
  const RULE_FILTER        = 'filter'; // Will modify raw input as it moves through this validation step

  protected $_rules        = [],    // Stores all keys, names, and associated rules to be applied
            $_cage_data    = [],    // Pre-tested, unvalidated data
            $_valid_data   = [],    // Post-tested, approved data
            $_field_names  = [],    // list of rules to their readable names
            $_errors       = [],    // Key => Message for an errors encountered
            $_run_complete = false,
            $_delimiter    = ', ';

  protected $_rules_provider;


  /**
   * @param array           $cage_data       what key => value pairs will be checked
   * @param RulesInterface  $rules_provider  which checks are available
   */
  public function __construct( $cage_data = [], RulesProvider $rules_provider = null ) {

    // When $cage_data is not an array, there will be no data to be validated (at all)
    if ( is_array( $cage_data ) ) {
      $this->setCageData( $cage_data );
    }

    if ( $rules_provider ) {
      $this->setRulesProvider( $rules_provider );
    }

  } // __construct


  /**
   * Key-value pair of data to validate
   *
   * @param array $data
   */
  public function setCageData( array $data ) {

    return $this->_cage_data = $data;

  } // setCageData


  /**
   * @return array
   */
  public function getCageData() {

    return $this->_cage_data;

  } // getCageData


  /**
   * @param string $key  retrieve unfiltered data associated by $key
   *
   * @return mixed|null  null when non-existant in caged data
   */
  public function getCageDataValue( $key ) {

    return ( isset( $this->_cage_data[ $key ] ) )
           ? $this->_cage_data[ $key ]
           : null;

  } // getCageDataValue


  /**
   * Set validation rules for a field.
   *
   * @param string $key        index where to expect data to validate
   * @param string $fieldname  readable name for this data field
   * @param string $rules      pipe-delimited or array series of validation rules to be applied in order
   *
   * @return $this   providing a fluent interface
   */
  public function setRule( $key, $fieldname, $rules ) {

    $rules = ( is_array( $rules ) )
             ? $rules
             : explode( '|', $rules );

    // IMPORTANT: simply having 'required' is not sufficient for a validator ruleset
    if ( count( $rules ) === 1 && $rules[0] === self::RULE_REQUIRED ) {
      throw new RuleRequirementException( "A valid ruleset for '{$key}' must more than just 'required'" );
    }

    $this->_rules[ $key ]       = $rules;
    $this->_field_names[ $key ] = $fieldname;

    return $this;

  } // setRule


  /**
   * Convenience function to set validation rules for multiple fields at the same time
   * @throws InvalidRuleException  when not enough elements are available in each rule grouping
   *
   * @param array $rule_groups
   *
   * @return $this  fluent interface
   */
  public function setRules( array $rule_groups ) {

    $parameters = 3;

    foreach ( $rule_groups as $rule_group ) {

      if ( count( $rule_group ) !== $parameters ) {
        throw new InvalidRuleException( "{$parameters} parameters required for setRule, " . count( $rule_group ) . " given" );
      }

      list( $key, $fieldname, $validators ) = $rule_group;

      $this->setRule( $key, $fieldname, $validators );

    } // foreach rules

    return $this;

  } // setRules


  /**
   * Programmatically add another rule to an EXISTING set
   *
   * @param string $key
   * @param string $rule
   *
   * @return $this  fluent interface
   */
  public function appendRule( $key, $rule ) {

    if ( empty( $this->_rules[ $key ] ) ) {
      throw new InvalidRuleException( "Key {$key} not yet set, cannot be appended" );
    }

    $this->_rules[ $key ][] = $rule;

    return $this;

  } // appendRule


  /**
   * @param string $key
   *
   * @return string   empty when not defined
   */
  public function getFieldName( $key ) {

    return ( isset( $this->_field_names[ $key ] ) )
           ? $this->_field_names[ $key ]
           : '';

  } // getFieldName


  /**
   * Return all keys marked for validation.
   *
   * @return array
   */
  public function getFields() {

    return array_keys( $this->_rules );

  } // getFields


  /**
   * Retrieves rules for all or a specific field to be validated
   *
   * @throws InvalidRuleException  when $key is supplied, but hasn't been set previously
   *
   * @param string $key  only retrieves rules for that field
   *
   * @return string|array
   */
  public function getFieldRules( $key ) {

    $rules = $this->getAllFieldRules();

    if ( empty( $rules[ $key ] ) ) {
      throw new InvalidRuleException( "Missing rules for '{$key}'" );
    }

    return $rules[ $key ];

  } // getFieldRules


  /**
   * @return array
   */
  public function getAllFieldRules() {

    return $this->_rules;

  } // getAllFieldRules


  /**
   * Convenience function to check if $key has been defined as required or not.
   *
   * @param string $key
   *
   * @return bool
   */
  public function isFieldRequired( $key ) {

    $rules = $this->getFieldRules( $key );

    return in_array( self::RULE_REQUIRED, $rules );

  } // isFieldRequired


  /**
   * Execute all validators on $this->_cage_data using setRule(s)
   *
   * @throws NotRunException           when no validators have previously been set
   * @throws RuleRequirementException  when rules are not configured correctly, lack arguments, etc.
   * @throws InvalidRuleException      when rule is invalid or its parameters are incorrect
   *
   * @return bool  pass or failed for all rules
   */
  public function run() {

    $rule_set = $this->getAllFieldRules();

    if ( empty( $rule_set ) ) {
      throw new NotRunException( "No validation rules to execute" );
    }

    $rules_provider = $this->getRulesProvider();

    // Each piece of $rule_set contains the input key followed by an array of actual rules
    foreach ( $rule_set as $key => $rules ) {

      $field_failed = false; // Flag this true to end validating field
      $raw_data     = $this->getCageDataValue( $key );


      if ( $raw_data === null ) {

        // Special Case: stop processing when data is completely missing, involve required rule

        if ( in_array( self::RULE_REQUIRED, $rules ) ) {

          $context       = $this->_buildErrorContext( $key );
          $required_rule = $rules_provider->getRule( self::RULE_REQUIRED );
          $error         = $this->_buildErrorFormatter( $required_rule, $context );

          $this->_addError( $key, $error );

        } // if in_array required

        // Do not process any additional rules
        continue;

      } // if raw_data

      // Define default context to be passed to called rules
      $context = [
          'key'        => $key,
          'validator'  => $this
      ];

      // Each rule for the specified key
      foreach ( $rules as $rule ) {

        list( $rule_name, $rule_parameters ) = $this->_processRuleIntoFunctionAndArguments( $rule, $key );

        $rule_component = $rules_provider->getRule( $rule_name );

        // Add/override additional context for rule about to be called
        $context['rule_name']  = $rule_name;
        $context['parameters'] = $rule_parameters;

        //==============================================================================

        // TODO: remove the need to handle filtering separately
        if ( $rule_name == self::RULE_FILTER ) {

          // IMPORTANT: send raw_data twice, the 2nd being pass by reference
          $closure      = $rule_component->getClosure();
          $field_failed = !$closure( $raw_data, $context, $raw_data );

        } // if rule_name = filter

        else {
          $field_failed = !$rule_component->isValid( $raw_data, $context );
        }

        //==============================================================================

        // Now that validation rule has run, check the results
        if ( $field_failed ) {

          $error_context   = $this->_buildErrorContext( $key, $rule_parameters );
          $error_formatter = $this->_buildErrorFormatter( $rule_component, $error_context );

          $this->_addError( $key, $error_formatter );
          break;

        } // if field_failed

      } // foreach rules

      // On successfully passing all rules, move data to validated array
      if ( !$field_failed ) {
        $this->_valid_data[ $key ] = $raw_data;
      }

    } // foreach rules_set

    $this->_run_complete = true;

    return !$this->_hasErrors();

  } // run


  /**
   * @throws FailureException on failure
   *
   * @return bool
   */
  public function runStrict() {

    $valid = $this->run();

    if ( !$valid ) {

      throw new FailureException( $this->getAllFieldErrorMessagesString(), 0, null, $this );
    }

    return $valid;

  } // runStrict


  /**
   * Return all valid data.
   *
   * @return array
   */
  public function getValidatedData() {

    return $this->_valid_data;

  } // getValidatedData


  /**
   * Return all fields that failed validation
   *
   * @return array
   */
  public function getFailedFields() {

    return array_keys( $this->_errors );

  } // getFailedFields


  /**
   * Produces a single string of failures to be returned to client
   *
   * @param string $field      which error to locate
   * @param string $delimiter  optionally override preset
   *
   * @return string  as fieldname is not
   */
  public function getFieldErrorMessage( $field, array $context = [] ) {

    if ( !isset( $this->_errors[ $field ] ) ) {
      return '';
    }

    $error = $this->_errors[ $field ];

    return ( $error instanceof ErrorFormatter )
           ? $error->render( $context )
           : $error;

  } // getFieldErrorMessage


  /**
   * @return array   each error, keyed by failing field
   */
  public function getAllFieldErrorMessages() {

    $fields = $this->getFailedFields();
    $result = [];

    foreach ( $fields as $field ) {
      $result[ $field ] = $this->getFieldErrorMessage( $field );
    }

    return $result;

  } // getAllFieldErrorMessages


  /**
   * @param string $delimiter
   *
   * @return string
   */
  public function getAllFieldErrorMessagesString( $delimiter = ', ' ) {

    $results = $this->getAllFieldErrorMessages();

    $results = array_values( $results );

    return implode( $delimiter, $results );

  } // getAllFieldErrorMessagesString


  /**
   * Checks for a failure in an individual key
   */
  public function isFieldFailed( $key ) {

    $error_keys = $this->getFailedFields();

    return ( in_array( $key, $error_keys ) );

  } // isFieldFailed


  /**
   * Allows a validation callback function to add a custom error to a field
   *
   * @param string $field    which one failed
   * @param string $message  error template following structure identical to any rule message template
   */
  public function addFieldFailure( $field, $message ) {

    $context = $this->_buildErrorContext( $field );
    $rule    = new CallbackTemplateRule();

    $rule->setErrorTemplate( $message );

    $error   = $this->_buildErrorFormatter( $rule, $context );

    // Ensures this is a valid field
    $this->getFieldRules( $field );

    $this->_addError( $field, $error );

  } // addFieldFailure


  /**
   * @param RulesProviderInterface $rules
   */
  public function setRulesProvider( RulesProviderInterface $rules_provider ) {

    $this->_rules_provider = $rules_provider;

  } // setRulesProvider


  /**
   * @return RulesProviderInterface $rules
   */
  public function getRulesProvider() {

    if ( empty( $this->_rules_provider ) ) {
      $this->_rules_provider = new RulesProvider();
    }

    return $this->_rules_provider;

  } // getRulesProvider


  /**
   * When creating error messaging, what will separate individual messages
   *
   * @param string $delimiter
   *
   * @return $this  for fluent interface
   */
  public function setMessageDelimiter( $delimiter ) {

    $this->_delimiter = $delimiter;

    return $this;

  } // setMessageDelimiter


  /**
   * @return string
   */
  public function getMessageDelimiter() {

    return $this->_delimiter;

  } // getMessageDelimiter


  /**
   * @throws InvalidRuleException  when attempting to grab keys where rules have not been set
   *
   * @param mixed|null $key  null when not available
   */
  public function getValidatedField( $key ) {

    if ( !isset( $this->_rules[ $key ] ) ) {
      throw new InvalidRuleException( "Call ->setRule() for '{$key}' first" );
    }

    if ( !$this->_isRunComplete() ) {
      throw new NotRunException( "Validator must be ->run() before retrieving values" );
    }

    // If data exists and isn't invalid, return it, otherwise return an empty string
    return ( isset( $this->_valid_data[ $key ] ) )
           ? $this->_valid_data[ $key ]
           : null;

  } // getValidatedField


  /**
   * After ->run(), retrieve a list of keys that did pass
   *
   * @return array
   */
  public function getValidatedFields() {

    return array_keys( $this->_valid_data );

  } // getValidatedFields


  /**
   * Convenience and alias for ->getValidatedField()
   *
   * @param string
   *
   * @return mixed
   */
  public function __get( $key ) {

    return $this->getValidatedField( $key );

  } // __get


  /**
   * @throws BadMethodCallException  method is not supported
   */
  public function __set( $key, $value ) {

    $key;   // Appease PHPMD
    $value;

    throw new \BadMethodCallException( "Magic properties are disabled" );

  } // __set


  protected function _isRunComplete() {

    return $this->_run_complete;

  } // _isRunComplete


  /**
   * Adds both a default error message with out without a fieldname, plus adds the error key into an array
   *
   * @param string $key
   * @param NBD\Validation\Formatters\ErrorFormatter $error
   */
  protected function _addError( $key, ErrorFormatter $error ) {

    // Ensure a field that has errors cannot possibly receive this data
    unset( $this->_valid_data[ $key ] );

    $this->_errors[ $key ] = $error;

  } // _addError


  /**
   * Determines if any fields have failed validation
   *
   * @return bool
   */
  protected function _hasErrors() {

    return !empty( $this->_errors );

  } // _hasErrors


  /**
   * @param mixed  $rule
   * @param string $key   field currently being processed
   *
   * @return array [ 0 => function name/Closure, 1 => optional array of parameters ]
   */
  protected function _processRuleIntoFunctionAndArguments( $rule, $key ) {

    $rule_parameters = [];

    if ( $rule instanceof \Closure ) {
      $rule = $this->_convertToCallableName( $rule );
    }

    else {
      // When a [ appears anywhere, this is an attempt to use a rule as parameterized function
      $param_position = strpos( $rule, '[' );

      if ( $param_position !== false )  {

        // When there's no complimenting bracket, this is a problem
        if ( substr( $rule, -1 ) !== ']' )  {
          throw new RuleRequirementException( "Field '{$key}' needs rule parameters encapsulated by []" );
        }

        // Remove the brackets from the request, leaving a (hopefully) comma-separated list of parameters
        $rule_arguments  = substr( $rule, ( $param_position + 1 ), strlen( $rule ) );

        // Remove parameters from the rule name
        $rule            = substr( $rule, 0, $param_position );
        $rule_arguments  = substr( $rule_arguments, 0, ( strlen( $rule_arguments ) - 1 ) );

        // Create an array by dividing arguments along the comma
        $rule_parameters = explode( ',', $rule_arguments );

      } // if param_position

    } // else (!closure)

    // Standardize rules with lowercase first character
    return [ lcfirst( $rule ), $rule_parameters ];

  } // _processRuleIntoFunctionAndArguments


  /**
   * Provides backwards compatibility for existing callback rules
   *
   * @param Closure $rule
   *
   * @return string
   */
  protected function _convertToCallableName( \Closure $rule ) {

    // TODO: move this assignment implementation into RulesProvider
    $new_name = spl_object_hash( $rule );

    $this->getRulesProvider()->setCallbackRule( $new_name, $rule );

    return $new_name;

  } // _convertToCallableName


  /**
   * @param string $key
   * @param array  $rule_parameters  parameters extracted from the rule definition on creation
   *
   * @return array
   */
  protected function _buildErrorContext( $key, array $rule_parameters = [] ) {

    return [
        'fieldname'  => $this->getFieldName( $key ),
        'field'      => $key,
        'parameters' => $rule_parameters
    ];

  } // _buildErrorContext


  /**
   * @param RuleInterface $rule
   * @param array         $context
   *
   * @return ErrorFormatter
   */
  protected function _buildErrorFormatter( RuleInterface $rule, array $context ) {

    return new ErrorFormatter( $rule, $context );

  } // _buildErrorFormatter

} // ValidatorService
