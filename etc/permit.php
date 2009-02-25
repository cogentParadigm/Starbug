#!/usr/bin/php
<?php
/**
* FILE: etc/permit.php
* PURPOSE: This file sets up permissions appropriately 
* NOTE: you should run this from the command line as root, before or immediately after install.php.
*
* This file is part of StarbugPHP
*
* StarbugPHP - meta content manager
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

	//COLLECT USER INPUT
	fwrite(STDOUT, "\nWelcom to the StarbugPHP Pre-install\nPlease enter the following information:\n(NOTE: You should be running this script as root)\n\ndevelopment UNIX username:");
	$devuser = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "Webserver UNIX username:");
	$webuser = str_replace("\n", "", fgets(STDIN));
	
	//SET PERMISSIONS
	exec("groupadd starbug");
	exec("usermod -a -G starbug $devuser");
	exec("usermod -a -G starbug $webuser");
	exec("chgrp -R starbug core/db/schema");
	exec("chgrp starbug script/_generate/*");
	exec("chmod -R 664 core/db/schema");
	exec("chmod ug+s script/_generate/*");
	
	fwrite(STDOUT, "Now please run etc/install.php as $devuser");
