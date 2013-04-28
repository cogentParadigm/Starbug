<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file etc/Etc.php The project wide configuration file
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup etc
 */
include(BASE_DIR."/etc/Host.php");
/**
 * holds configuration constants
 * access configuration constants via this class using Etc::CONSTANT_NAME
 * @ingroup etc
 */
class Etc extends Host {

	/**
	 * THIS FILE IS DEPRECATED
	 * settings are stored in the settings table and accessed via the settings function
	 * eg. $site_name = settings("site_name");
	 */
	

}
?>
