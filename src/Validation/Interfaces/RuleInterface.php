<?php

namespace Behance\NBD\Validation\Interfaces;

interface RuleInterface {

  /**
   * Inform the caller is $data fits the criteria of the implementing rule
   *
   * @param mixed $data     item to be judged by implementing rule
   * @param array $context  information to assist in determining validity of $data
   *
   * @return bool
   */
  public function isValid( $data, array $context = null );


  /**
   * @return string  templated (not rendered) message to use if $this rule fails
   */
  public function getErrorTemplate();


  /**
   * Allows rule to format additional replacements
   * Children can override this method, inserting fields into $context to be used for template replacement
   *
   * @param array $context
   *
   * @return array
   */
  public function convertFormattingContext( array $context  );

} // RuleInterface
