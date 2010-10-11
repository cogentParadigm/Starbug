<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/external.php external init file. can be included by 3rd party apps
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("core", "", dirname(__FILE__)));
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
