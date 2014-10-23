<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Represents the container of a rule to be "run"
 */
class FloatRule extends RegexRuleAbstract {

  protected $_pattern = '/^[+-]?(?:(?:(?:(?:\d+|(?:(?:\d*\.\d+)|(?:\d+\.\d*)))[eE][+-]?\d+))|(?:(?:\d*\.\d+)|(?:\d+\.\d*))|\d+)$/';

} // FloatRule
