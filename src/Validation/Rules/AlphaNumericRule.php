<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Represents the container of a rule to be "run"
 */
class AlphaNumericRule extends RegexRuleAbstract {

  protected $_pattern = '/^[0-9A-Za-z\\pL\xC0-\xFF\xD8-\xF6\xF8-\xFF]+$/u';

} // AlphaNumericRule
