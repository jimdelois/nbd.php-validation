<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Validates that a value is possible HEX value.
 */
class HexColorRule extends RegexRuleAbstract {

  protected $_error_template = "%fieldname% must be a HEX color without #";

  protected $_pattern = '/^([0-9a-f]{3}|[0-9a-f]{6})$/i';

} // HexColorRule
