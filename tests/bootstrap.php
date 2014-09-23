<?php

  // need this or any date() call will complain
  date_default_timezone_set( 'UTC' );

  error_reporting( -1 ); // Show them all

  $root_directory = realpath( dirname( __FILE__ ) . '/../' ) . '/';

  require( $root_directory . 'vendor/autoload.php' );
