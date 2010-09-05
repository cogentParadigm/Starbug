<?php
/**
* FILE: core/init.php
* PURPOSE: provide application wide functionality
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
date_default_timezone_set('UTC');
error_reporting(E_ALL ^ E_NOTICE);
function empty_nan(&$val, $default="") {if(!isset($val) || !is_numeric($val)) $val = $default;}
function dfault(&$val, $default="") {if(!isset($val)) $val = $default;return $val;}
function efault(&$val, $default="") {if(empty($val)) $val = $default;return $val;}
function P($var) {return Etc::PREFIX.$var;}
function uri($path, $flags="") {
	if ($flags == "s") $prefix = "https://";
	else if ($flags == "f") $prefix = "";
	else $prefix = "http://";
	return $prefix.Etc::WEBSITE_URL.$path;
}
include(BASE_DIR."/core/db/db.php");
include(BASE_DIR."/core/sb.php");
include(BASE_DIR."/core/db/Table.php");
include(BASE_DIR."/util/starr.php");
global $sb;
$sb = new sb();
include(BASE_DIR."/etc/autoload.php");
?>
