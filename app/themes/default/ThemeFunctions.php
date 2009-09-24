<?php
/**
* FILE: app/themes/default/ThemeFunctions.php
* PURPOSE: default theme generate functions
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
class ThemeFunctions {
	function app() {
		exec("script/generate app");
	}
	function model() {
		exec("script/generate model $_POST[modelname]");
	}
	function crud() {
		$extra = "";
		if ($_POST['update']) {
			$extra .= " -u";
		}
		exec("script/generate crud $_POST[modelname]$extra");
	}
}
?>
