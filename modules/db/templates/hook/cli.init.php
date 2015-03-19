<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/db/hooks/cli.init.php cli init hook
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */

	/**
	 * global instance of the Schemer
	 * @ingroup global
	 */
	global $schemer;
	$schemer = new Schemer($sb->db, $modules);
	$sb->add_listener($schemer);
	$schemer->fill();
?>
