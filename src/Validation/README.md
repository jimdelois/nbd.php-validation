Validation Component
====================

Provides easy to use rules for checking various data types. Rules can be used
in isolation, or chained together for a form-like validation interface.

Usage
-----

#####Quickly validate a single data point


```
use Behance\NBD\Validation\Rules\IntegerRule;

$rule  = new IntegerRule();

$valid = $rule->isValid( 123 );  // true
$valid = $rule->isValid( 'abc' );  // false
```


#####Validate a complex key-value array of "caged" data, like an HTML form


```
use Behance\NBD\Validation\Services\ValidatorService;

$validator = new ValidatorService();

// Insert data to be validated
$validator->setCageData( $_POST );

// Define a series of rules: field key, field name, and sequence of validator rules
$validator->setRule( 'email',      'E-Mail',     'required|email' )
          ->setRule( 'first_name', 'First Name', 'required|alpha' )         // Can be pipe-separated
          ->setRule( 'last_name',  'Last Name',  [ 'required', 'alpha' ] ); // Or an array

// Obtain a single result for whether this data set passes the defined rules
$valid = $validator->run();

if ( !$valid ) {

  // During failures, retrieve error messages keyed by failing field
  $errors = $validator->getAllFieldErrorMessages();

  foreach ( $errors as $field => $message ) {
    // ...
  }

  // Or, retrieve a single error message string for all failures
  $error_message = $validator->getAllFieldErrorMessagesString();

  // Or, just retrieve the error message for email field
  $email_message = $validator->getFieldErrorMessage( 'email' );

  // Or, just retrieve the fields that failed
  $error_keys    = $validator->getFailedFields();

} // if !valid

else {

  // As a convenience, valid data points are available as magic properties
  $email = $validator->email;

  $first_name = $validator->first_name;
  $last_name  = $validator->last_name;

  // Or, retrieve without magic
  $email = $validator->getValidatedField( 'email' );


  // Retrieve fields that passed validation with values as a key-value array
  $fields = $validator->getValidatedData(); // ex. [ 'email' => 'xxx@email.com' ]

  // Check the inverse, did field fail?
  $email_valid = $validator->isFieldFailed( 'email' ); // false

  // Grab the text name assigned to field
  $field_name  = $validator->getFieldName( $key ); // E-Mail

} // else (valid)
```


#####Available 'simple' validator rules (and growing):


* alpha
* alphaNumeric
* array
* email
* decimal
* float
* integer
* json
* notEmpty
* url


#####Available parameterized validator rules

Parameters are defined comma-separated inside brackets after rule:

<table>
<tr><th>Rule           </th><th>Usage                        </th><th>Params</th><th>Explanation</th></tr>
<tr><td>matches        </td><td>matches[target]              </td><td>1 </td><td>does input match string 'target'</td></tr>
<tr><td>minLength      </td><td>minLength[5]                 </td><td>1 </td><td>is string input length >=5 characters (defaults UTF-8, not byte count)</td></tr>
<tr><td>maxLength      </td><td>maxLength[5]                 </td><td>1 </td><td>is string input length <=5 characters (defaults UTF-8, not byte count)</td></tr>
<tr><td>instanceOf     </td><td>instanceOf[stdClass]         </td><td>1 </td><td>is input an object and of type 'stdClass'</td></tr>
<tr><td>range          </td><td>range[1,10]                  </td><td>2 </td><td>is input between 1 and 10 (inclusive)</td></tr>
<tr><td>stringContains </td><td>stringContains[haystack]     </td><td>1 </td><td>is input a string and a needle for 'haystack'</td></tr>
<tr><td>callback       </td><td>callback[User,isUniqueEmail] </td><td>2 </td><td>calls User::isUniqueEmail( $input ), interprets results as a boolean</td></tr>
<tr><td>containedIn    </td><td>containedIn[abc,def,ghi]     </td><td>1+</td><td>input is in array of parameters (variable length)</td></tr>
</table>


#####Special parameterized rules:


<table>
<tr><th>Rule</th><th>Usage</th><th>Params</th><th>Explanation</th></tr>

<tr><td>filter</td><td>filter[trim,md5]</td><td>1+</td><td>applies transformation of input, evaluating left to right, transformation is seen by subsequent rules and subsequent retrieval. Parameter can be any single-parameter defined function that returns a transformed result. In example, will trim(), then md5() input, which would be coded as md5( trim( $input ) );</td></tr>

</table>


#####Add custom named validators quickly and easily:

```
use Behance\NBD\Validation\Services\ValidatorService;

$validator = new ValidatorService();
$rules     = $validator->getRulesProvider();

// Creates a rule that can be used independently, or as part of the validator service
$regex_rule = $rules->setRegexRule( 'myNewRegexRule', '/^abcdefg$/' );
$valid      = $regex_rule->isValid( 'abcdefg' );  // true


// Create a callback rule based on a boolean-returning closure, can also be used in both places
$closure = ( function( $data ) {
  return $data == 'hello';
} );

$callback_rule = $rules->setCallbackRule( 'hello', $closure );

$callback_rule->isValid( 'hello' );  // true
```
