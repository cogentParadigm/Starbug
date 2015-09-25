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
// initialize
include(dirname(__FILE__)."/cli.php");
?>
