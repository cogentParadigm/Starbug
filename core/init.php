<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/init.php the standard init file. provides application wide functionality
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
date_default_timezone_set('UTC');
error_reporting(E_ALL ^ E_NOTICE);

// include global functions
include(BASE_DIR."/core/global_functions.php");

// include the db class
include(BASE_DIR."/core/db/db.php");

// include the sb class
include(BASE_DIR."/core/sb.php");

// include the Table class
include(BASE_DIR."/core/db/Table.php");

// include the starr class
include(BASE_DIR."/util/starr.php");

/**
 * global instance of the sb class
 * @ingroup core
 */
global $sb;
$sb = new sb();
if (!is_array($autoload)) include(BASE_DIR."/etc/autoload.php");
call_user_func_array(array($sb, "import"), $autoload);
?>
