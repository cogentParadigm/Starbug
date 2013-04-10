<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/console.php initiates the console
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	passthru("php -d auto_prepend_file=core/cli.php -a");
?>
