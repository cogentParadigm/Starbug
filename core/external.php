<?php
/**
* FILE: core/external.php
* PURPOSE: for external apps to access starbug
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
session_start();
define('BASE_DIR', str_replace("core", "", dirname(__FILE__)));
//configure
include(BASE_DIR."/etc/Etc.php");
//initialize
include(BASE_DIR."/core/init.php");
//load request class, but do not instantiate
include(BASE_DIR."/core/Request.php");
?>
