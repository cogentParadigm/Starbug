<?php
/**
* FILE: etc/Host.php
* PURPOSE: This is the main configuration file
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
class Host {
	/* Log in details for database */
	const DB_TYPE = "mysql";
	const DB_HOST = "localhost";
	const DB_USERNAME = "root";
	const DB_PASSWORD = "";
	const DB_NAME = "test";

	/* URL of website */
	const WEBSITE_URL = "localhost/";

	/* Java paths */
	const JAVA_PATH = "/usr/bin/java";
	const SAXON_PATH = "/usr/local/saxon/saxon9.jar";
}
?>
