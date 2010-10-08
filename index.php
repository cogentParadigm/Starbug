<?php
/**
 * This file is part of StarbugPHP
 * @file index.php index file. handles browser requests
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
session_start();
/**
 * the base directory
 */
define('BASE_DIR', dirname(__FILE__));

// include the config file
include(BASE_DIR."/etc/Etc.php");

// include init file
include(BASE_DIR."/core/init.php");

// include Request
include(BASE_DIR."/core/Request.php");

/**
 * global instance of the Request class
 * @ingroup core
 */
global $request;
$request = new Request($groups, $statuses);
$request->set_path($_SERVER['REQUEST_URI'], end(explode("/",dirname(__FILE__))));
$request->execute();
?>
