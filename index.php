<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file index.php index file. handles browser requests
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */

// include init file
include("core/init.php");

/**
 * global instance of the Request class
 * @ingroup global
 */
global $request;
$request = new Request();
$request->set_path($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
$request->execute();
?>
