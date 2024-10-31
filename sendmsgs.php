<?php
/**
 * Plugin Name: Sendmsg Elementor Addon
 * Description: Plugin to extend Elementor forms with Sendmsgs.
 * Version:     1.2
 * Author:      Comstarsystems
 * Author URI:  https://www.sendmsg.co.il
 * Text Domain: sendmsg-elements
 */

/*
Defining Constants
*/
define('SENDMSGS_VERSION','1.0');
/*
Sendmsgs API Main
*/
require_once('inc/main.php');

/*
Admin Panel Page
*/
require_once('inc/settings.php');

/*
Scripts
*/
require_once('inc/scripts.php');

/*
AJAX Handling
*/
require_once('inc/ajax.php');

/*
MISC Functions
*/
require_once('inc/misc.php');