<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Represents the container of a rule to be "run"
 */
class AlphaRule extends RegexRuleAbstract {

  protected $_pattern = '/^[A-Za-z\\pL\xC0-\xFF\xD8-\xF6\xF8-\xFF]+$/u';

} // AlphaRule
