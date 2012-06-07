<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file index.php index file. handles browser requests
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
session_start();

// include init file
include("core/init.php");

// include Request
include("core/Request.php");

/**
 * global instance of the Request class
 * @ingroup global
 */
global $request;
$request = new Request($groups, $statuses);
$request->set_path($_SERVER['REQUEST_URI'], end(explode("/",dirname(__FILE__))));
$request->execute();
?>
