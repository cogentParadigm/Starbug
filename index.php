<?php
// FILE: index.php
/**
 * index file. handles browser requests
 * 
 * @package StarbugPHP
 * @subpackage core
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
session_start();
/**
 * base directory
 */
define('BASE_DIR', dirname(__FILE__));
/**
 * include the configuration class
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
/**
 * the global instance of the Request class
 * @global Request $request
 * @name $request
 */
$request = new Request($groups, $statuses);
$request->set_path($_SERVER['REQUEST_URI'], end(explode("/",dirname(__FILE__))));
$request->execute();
?>
