<?php

namespace Behance\NBD\Validation\Rules;

use Behance\NBD\Validation\Abstracts\RegexRuleAbstract;

/**
 * Represents the container of a rule to be "run"
 */
class UrlRule extends RegexRuleAbstract {

  protected $_pattern = '/^((?:https?):\/\/)?(?:(?:(?:[\w\.\-\+!$&\'\(\)*\+,;=_]|%[0-9a-f]{2})+:)*(?:[\w\.\-\+%!$&\'\(\)*\+,;=_]|%[0-9a-f]{2})+@)?(?:[A-Za-z0-9_\-]+\.)(?:[A-Za-z0-9_\-\.])+(?::[0-9]+)?(?:[\/|\?](?:[\w#!:\.\?\+=&@$\'~*,;_\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/u';

} // UrlRule
