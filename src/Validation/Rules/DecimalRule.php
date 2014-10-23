<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Represents the container of a rule to be "run"
 */
class DecimalRule extends RegexRuleAbstract {

  protected $_pattern = '/^\-?[0-9]+(\.[0-9]+)?$/u';

} // DecimalRule
