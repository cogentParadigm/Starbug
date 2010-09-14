<?php
// FILE: core/external.php
/**
 * external init file. can be included by 3rd party apps
 * 
 * @package StarbugPHP
 * @subpackage core
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
session_start();
/**
 * the base directory
 */
define('BASE_DIR', str_replace("core", "", dirname(__FILE__)));
/**
 * include the configuration file
 */
include(BASE_DIR."/etc/Etc.php");
/**
 * include the init file
 */
include(BASE_DIR."/core/init.php");
/**
 * include the Request class
 */
include(BASE_DIR."/core/Request.php");
?>
