<?php
/**
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
//configure
include("etc/Etc.php");
$groups = array(
	"root"			=> 1,
	"user"			=> 2
);
$statuses = array(
	"deleted"     => 1,
	"pending"     => 2,
	"public"		  => 4,
	"private"			=> 8
);
//initialize
include("etc/init.php");
//go
include("core/Request.php");
$request = new Request($groups, $statuses);
$request->set_path($_SERVER['REQUEST_URI'], end(explode("/",dirname(__FILE__))));
$request->execute();
?>
