<?php

namespace NBD\Validation\Interfaces;

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

} // RuleInterface
