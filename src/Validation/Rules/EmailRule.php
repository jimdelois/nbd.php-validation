<?php

namespace NBD\Validation\Rules;

use NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Represents the container of a rule to be "run"
 */
class EmailRule extends RegexRuleAbstract {

  protected $_pattern = '/^[A-Za-z0-9_\.\+-]+@[A-Za-z0-9_\.-]+\.[A-Za-z0-9_-]+$/u';

} // EmailRule
