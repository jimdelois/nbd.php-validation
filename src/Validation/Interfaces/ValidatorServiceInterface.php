<?php

namespace Behance\NBD\Validation\Interfaces;

interface ValidatorServiceInterface {

  /**
   * Key-value pair of data to validate
   *
   * @param array $data
   */
  public function setCageData( array $key );


  /**
   * @return array
   */
  public function getCageData();


  /**
   * @param string $key  retrieve unfiltered data associated by $key
   *
   * @return mixed|null  null when non-existant in caged data
   */
  public function getCageDataValue( $key );


  /**
   * Set validation rules for a field.
   *
   * @param string $key        index where to expect data to validate
   * @param string $fieldname  readable name for this data field
   * @param string $rules      pipe-delimited series of validation rules to be applied in order
   *
   * @return $this   providing a fluent interface
   */
  public function setRule( $key, $fieldname, $rules );


  /**
   * Convenience function to set validation rules for multiple fields at the same time
   * @throws InvalidRuleException  when not enough elements are available in each rule grouping
   *
   * @param array $rule_groups
   *
   * @return $this  for fluent interface
   */
  public function setRules( array $rule_groups );


  /**
   * Programmatically add another rule to an EXISTING set
   *
   * @param string $key
   * @param string $rule
   */
  public function appendRule( $key, $rule );


  /**
   * @param string $key
   *
   * @return string   empty when not defined
   */
  public function getFieldName( $key );

  /**
   * Retrieves rules for all or a specific field to be validated
   *
   * @throws InvalidRuleException  when $key is supplied, but hasn't been set previously
   *
   * @param string $key  only retrieves rules for that field
   *
   * @return string|array
   */
  public function getFieldRules( $key );


  /**
   * @return array
   */
  public function getAllFieldRules();


  /**
   * Convenience function to check if $key has been defined as required or not.
   *
   * @param string $key
   *
   * @return bool
   */
  public function isFieldRequired( $key );


  /**
   * Execute all validators on $this->_cage_data using setRule(s)
   *
   * @throws NotRunException           when no validators have previously been set
   * @throws RuleRequirementException  when rules are not configured correctly, lack arguments, etc.
   * @throws InvalidRuleException      when rule is invalid or its parameters are incorrect
   *
   * @return bool  pass or failed for all rules
   */
  public function run();


  /**
   * @throws FailureException on failure
   *
   * @return bool
   */
  public function runStrict();


  /**
   * Return all valid data.
   *
   * @return array
   */
  public function getValidatedData();


  /**
   * IMPORTANT: This method will not output ANYTHING if the supplied source has no members (ex. $_POST is empty, before form is posted)
   *
   * @param string $key
   *
   * @return string
   */
  public function getFieldErrorMessage( $key );


  /**
   * IMPORTANT: This method will not output ANYTHING if the supplied source has no members (ex. $_POST is empty, before form is posted)
   *
   * @return string   each error (if no key is supplied, otherwise a single error) wrapped in the start and end delimiter
   */
  public function getAllFieldErrorMessages();


  /**
   * Return all invalid keys.
   *
   * @return array
   */
  public function getFailedFields();


  /**
   * Checks for a failure in an individual key
   */
  public function isFieldFailed( $key );


  /**
   * Allows a validation callback function to add a custom error to a field
   *
   * @param string $key      validator key to tie this error message
   * @param string $message  error to display for $key, otherwise defaults to generic 'failed validation'
   */
  public function addFieldFailure( $key, $message );


  /**
   * After ->run(), retrieve a list of keys that did pass
   *
   * @return array
   */
  public function getValidatedFields();


  /**
   * @param RulesProviderInterface $rules
   */
  public function setRulesProvider( RulesProviderInterface $rules_provider );


  /**
   * @return RulesProviderInterface $rules
   */
  public function getRulesProvider();


  /**
   * When creating error messaging, what will separate individual messages
   *
   * @param string $delimiter
   *
   * @return $this  for fluent interface
   */
  public function setMessageDelimiter( $delimiter );


  /**
   * @return string
   */
  public function getMessageDelimiter();


  /**
   * @throws InvalidRuleException  when attempting to grab keys where rules have not been set
   *
   * @param mixed|null $key  null when not available
   */
  public function getValidatedField( $key );


} // ValidatorServiceInterface
