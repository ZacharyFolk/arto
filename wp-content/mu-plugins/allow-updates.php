<?php
/*
  Plugin Name: Allow Updates
  Description: Override to allow automatic updates under versioned control
  Version: 0.1
  Author: Zac
  Author URI:
*/

add_filter( 'automatic_updates_is_vcs_checkout', '__return_false', 1 );
