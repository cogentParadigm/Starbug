<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
$filename = array_shift($argv);
foreach($argv as $i => $q) $argv[$i] = explode(":", $q);
$css = file_get_contents($filename);

//CLEAR EXTRA CHARS
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
$css = str_replace("\n", "", $css);
$search = array("/{\s+/", "/\s+{/", "/;\s+/", "/:\s+/", "/}\s+/", "/;}/", "/\s+}/");
$replace = array("{", "{", ";", ":", "}", "}", "}");
$css = preg_replace($search, $replace, $css);
$css = str_replace("}", "}\n", $css);
$css = explode("\n", rtrim($css, "\n"));
// NOW THERE IS ONLY ONE RULE PER LINE

$rules = $properties = array();

// NOW WE LOOP THROUGH EACH RULE AND FILL THE RULES ARRAY
// array("selector" => array("property" => "value"))
foreach($css as $rule) {
	$rule = explode("{", rtrim($rule, '}'));
	$selectors = explode(",", $rule[0]);
	$props = explode(";", $rule[1]);
	$p = array();
	foreach($props as $prop) {
		$prop = explode(":", $prop);
		$p[$prop[0]] = $prop[1];
	}
	foreach($selectors as $s) {
		$s = trim($s);
		if(isset($rules[$s])) $rules[$s] = array_merge_recursive($rules[$s], $p);
		else $rules[$s] = $p;
	}
}

foreach($rules as $selector => $props) {
	foreach($props as $prop => $val) {
		if (!isset($properties[$prop])) $properties[$prop] = array();
		if (!isset($properties[$prop][$val])) $properties[$prop][$val] = array($selector);
		else $properties[$prop][$val][] = $selector;
	}
}
$typography = array(
	"font", "font-size", "font-weight", "font-variant", "font-style", "font-family", "font-stretch", "word-spacing", "word-wrap",
	"text-indent", "text-shadow", "text-align", "text-transform", "text-decoration", "line-height", "letter-spacing", "color",
	"background", "background-color", "background-image", "background-position", "background-repeat"
);
$structure = array(
	"display", "float", "position", "top", "right", "bottom", "left", "overflow", "width", "height", "z-index", "visiblity", "clear",
	"margin", "margin-top", "margin-right", "margin-bottom", "margin-left",
	"padding", "padding-top", "padding-right", "padding-bottom", "padding-left"
);

$output = array();
$sets = array($typography, $structure);

foreach($properties as $prop => $vals) {
	foreach($vals as $v => $s) {
		$propstr = $prop.":".$v;
		$num_sels = count($s);
		$sels = join(", ", $s);
		$str = $sels."{".$propstr."}";
		for($i=1;$i<$num_sels;$i++) $propstr .= $propstr;
		if(strlen($propstr) > strlen($str)) {
			$rules[$sels] = array($prop => $v);
			foreach($s as $sel) unset($rules[$sel][$prop]);
		}
	}
}

foreach($sets as $set) {
	$out = array();
	foreach($rules as $s => $props) {
		foreach($props as $p => $v) {
			if(in_array($p, $set)) {
				if(!isset($out[$s])) $out[$s] = array();
				$out[$s][$p] = $v;
			}
		}
	}
	$output[] = $out;
}

$final = "";
foreach($output as $o) {
	ksort($o);
	foreach($o as $s => $props) {
		$final .= $s."{";
		foreach($props as $p => $v) $final .= $p.":".$v.";";
		rtrim($final, ';');
		$final .= "}\n";
	}
}

$file = fopen("properties.css", "wb");
fwrite($file, $final);
fclose($file);
//foreach($ss as $k => $v) echo $k."\n";
/*print_r($properties);exit();
foreach($properties as $property => $values) {
	//print_r($values); exit();
	echo $property."\n";
	foreach($values as $v => $s) echo "  ".$v.":".join(", ", $s)."\n";
}
foreach($rules as $selector => $props) {
	echo $selector."\n  ";
	foreach($props as $p => $v) echo $p.":".$v;
}
*/
?>
