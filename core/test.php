<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/init.php the standard init file. provides application wide functionality
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
//define default database
if (!defined("DEFAULT_DATABASE")) define("DEFAULT_DATABASE", "test");

define("SB_TEST_MODE", true);

if (!defined("SB_CLI")) define("SB_CLI", true);

// initialize
include(dirname(__FILE__)."/init.php");

foreach ($locator->locate("autoload.php", "tests") as $global_include) include($global_include);
foreach ($locator->locate("global_functions.php", "tests") as $global_include) include($global_include);

?>
