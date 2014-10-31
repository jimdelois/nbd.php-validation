<?php

namespace Behance\NBD\Validation\Formatters;

use Behance\NBD\Validation\Interfaces\RuleInterface;

/**
 * Provides a container to turn a $rule and its associated error template into a renderable message
 * Each instance encapsulates a single failed $rule
 */
class ErrorFormatter {

  const FIELDNAME_DEFAULT       = 'This field';
  const REPLACEMENT_PLACEHOLDER = '%';

  // Data that get automatically pushed into all templates
  protected $_context_default =  [
      'fieldname' => self::FIELDNAME_DEFAULT,
  ];


  /**
   * @var RuleInterface  what is associated with the current error
   */
  protected $_rule;

  /**
   * @var array  used for variable replacement in templates
   */
  protected $_context = [];


  /**
   * @param RuleInterface $rule
   * @param array         $context
   */
  public function __construct( RuleInterface $rule, array $context = [] ) {

    $this->_rule     = $rule;
    $this->_context  = $context;

  } // __construct


  /**
   * @return array
   */
  public function getContext() {

    return $this->_context;

  } // getContext


  /**
   * Which rule is associated with $this error
   *
   * @return RuleInterface
   */
  public function getRule() {

    return $this->_rule;

  } // getRule


  /**
   * Replaces template variables with context variables
   *
   * @param array $context
   *
   * @return string
   */
  public function render( array $context = [] ) {

    $left = $right = self::REPLACEMENT_PLACEHOLDER;

    $context = $this->_getCombinedContext( $context );
    $output  = $this->_rule->getErrorTemplate();

    foreach ( $context as $key => $value ) {

      if ( !is_string( $value ) ) {
        continue;
      }

      $search = $left . $key . $right;
      $output = str_replace( $search, $value, $output );

    } // foreach context

    return $output;

  } // render


  /**
   * @param array $context
   *
   * @return array
   */
  protected function _getCombinedContext( array $context ) {

    $defaults = $this->_context_default;

    // Merge context with defaults, in priority order: passed context --> predefined context --> defaults
    if ( !empty( $context ) ) {

      $context = ( !empty( $this->_context ) )
                 ? $context
                 : array_merge( $this->_context, $context );

    } // if context

    else {
      $context = $this->_context;
    }

    return ( empty( $context ) )
           ? $defaults
           : array_merge( $defaults, $context );

  } // _getCombinedContext

} // ErrorFormatter
