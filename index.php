<?php
/**
* Starbug - PHP web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
define("BASE_DIR", end(split("/",dirname(__FILE__))));
//configure
include("etc/Etc.php");
//initialize
include("etc/init.php");
//go\
include("core/Request.php");
new Request($db);
?>
