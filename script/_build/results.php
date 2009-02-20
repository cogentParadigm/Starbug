<?php
/**
* FILE: script/_build/results.php
* PURPOSE: Template language parser
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
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
ini_set("memory_limit", "256M");
//build results script
require_once $topdir."etc/Etc.php";
require_once $topdir."etc/init.php";
require_once $topdir."util/Starr.php";
require_once $topdir."util/HT.php";
require_once $topdir."util/Form.php";

$implied_attrs = rA("meta=\nname,content\t,headr=\nhttp-equiv,content\t,lnk=\nrel,href\t,img=\nsrc,alt\t,a=\nhref\t");

//think about it: <: <: :> <: :> :>
function find_template_closer($str, $offset=0) {
	$close = strpos($str, ":>");
	$nextopen = strpos($str, "<:");
	$numopen = 0;
	if (($nextopen === false) || ($nextopen > $close)) return $offset+$close;
	else {
		while (($nextopen !== false) && ($nextopen < $close)) {
			$close = strpos($str, ":>", $close+2);
			$nextopen = strpos($str, "<:", $nextopen+2);
		}
		if ($close !== false) return $offset+$close;
	}
}

function parse_template($resultString) {
	$open = strpos($resultString, "<:");
	if ($open === false) return $resultString;
	else $close = find_template_closer(substr($resultString, $open+2), $open+2);
	$tag = trim(substr($resultString, $open+2, $close-($open+2)));
	$taglength = strlen($tag);
	if ($close !== false) return substr($resultString, 0, $open).parse_template(parse_tag($tag, substr($resultString, ($open+2+$taglength), $close-($open+2+$taglength))).substr($resultString,$close+2));
	else return parse_template(substr($resultString, 0, $open)."ERROR: UNPARSED TAG".substr($resultString,$close+2));
}

function parse_tag($innards, $suffix) {
//looks like: ^TagName( tag_args)*( attr=value)*(>|$)(.*)$
//index in tokens array: TagName:1 ( tag_args)*:2 ( attr=value)*:4 (>|$):6 (.*):7
	preg_match("#^([a-z\d]+)((\s+[^\s=>]+)*)((\s+[^\s=]+=[^\s=>]+)*)\s*(>|$)(.*)#s", $innards, $tokens);
// 	echo $innards."\n\n";
	if (count($tokens) == 8) return "<".$tokens[1].$tokens[2].add_attr_quotes($tokens[4]).$tokens[6].$tokens[7].$suffix.((empty($tokens[6]))?"/>":"<".$tokens[1]."/>");
	else return "ERROR_UNPARSED TAG: ".count($tokens);
}

function add_attr_quotes($attr) {
	$attr = explode(" ", trim($attr));
	$str = "";
	foreach ($attr as $at) {
		$at = explode("=", $at);
		if (count($at) == 2) $str .= " ".$at[0].'="'.$at[1].'"';
	}
	return $str;
}

$data = file_get_contents($topdir."app/results/".$argv[2].".php");
$data = str_replace("<?php", "<?", $data);
$data = str_replace("<?=", "<? echo", $data);
$php_open = strpos($data, "<?");
$php_blocks = array();
while ($php_open !== false) {
	$php_close = strpos($data, "?>", $php_open);
	$php_blocks[] = substr($data, $php_open+2, $php_close-($php_open+2));
	$php_open = strpos($data, "<?", $php_close+2);
}
$data = preg_replace("#<\?.*?\?>#s", "%{php}%", $data);
for($i=0;$i<count($php_blocks);$i++) $data = substr_replace($data, "%{".$i."}%", strpos($data, "%{php}%"), 7);
$data = parse_template($data);
for($i=0;$i<count($php_blocks);$i++) $data = str_replace("%{".$i."}%", "<?php".$php_blocks[$i]."?>", $data);
// fwrite(STDOUT, $data);
$file = fopen($topdir."app/output/".$argv[2].".php", "w");
fwrite($file, $data);
fclose($file);
