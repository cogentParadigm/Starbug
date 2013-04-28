<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/strings.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup strings
 */
/**
 * @defgroup strings
 * global functions
 * @ingroup global
 */
/**
 * @copydoc star
 * @ingroup strings
 */
function star($str=array()) {
	if (is_array($str)) return $str;
	$arr = array();
	$keypairs = explode("  ", $str);
	foreach($keypairs as $keypair) {
		if (false !== ($pos = strpos($keypair, ":"))) {
			$key = substr($keypair, 0, $pos);
			$value = substr($keypair, $pos+1);
			if ($value == "false") $value = false;
			if ($value == "true") $value = true;
			$arr[$key] = $value;
		} else if ($keypair!="") $arr[] = $keypair;
	}
	return $arr;
}
/**
 * prefix a variable with the site prefix
 * @ingroup strings
 * @param string $var the value to prefix
 * @return string the prefixed value
 */
function P($var) {return Etc::PREFIX.$var;}
/**
 * normalize a string
 * @ingroup strings
 * @param string $raw the raw string
 * @param string $valid_chars valid characters. default is 'a-zA-Z0-9'
 * @return string the normalized version of $raw
 */
function normalize($raw, $valid_chars='a-zA-Z0-9 ') {
	return preg_replace("/[^".$valid_chars."]/", "", $raw);
}
/**
 * get the plural form of a singular form word
 * @ingroup strings
 * @param string $singular the singular form of the word
 * @return string the plural form of the word
 */
function format_plural($singular) {
	$rules = array(
		'/(x¦ch¦ss¦sh)$/' => '\1es', # search, switch, fix, box, process, address 
		'/series$/' => '\1series', 
		'/([^aeiouy]¦qu)ies$/' => '\1y', 
		'/([^aeiouy]¦qu)y$/' => '\1ies', # query, ability, agency 
		'/(?:([^f])fe¦([lr])f)$/' => '\1\2ves', # half, safe, wife 
		'/sis$/' => 'ses', # basis, diagnosis 
		'/([ti])um$/' => '\1a', # datum, medium 
		'/person$/' => 'people', # person, salesperson 
		'/man$/' => 'men', # man, woman, spokesman 
		'/child$/' => 'children', # child 
		'/(.*)status$/' => '\1statuses', 
		'/s$/' => 's', # no change (compatibility) 
		'/$/' => 's' 
	);
	$plural = $singular; 
	foreach($rules as $pattern => $repl) { 
		$plural = preg_replace($pattern, $repl, $singular); 
		if ($singular != $plural) break; // leave if plural found 
	} 
	return $plural; 
}
/**
 * convert an array to an HTML attribute string
 * @ingroup strings
 * @param array $ops an associative array
 * @param bool $echo if true then echo out the attribute string (default is true)
 * @return string the HTML attribute string
 */
function html_attributes($ops, $echo=true) {
	$valid = array("abbr", "accept-charset", "accept", "accesskey", "action", "align", "alink", "alt", "archive", "axis", "background", "bgcolor", "cellpadding", "cellspacing", "char", "charoff", "charset", "checked", "cite", "class", "classid", "clear", "code", "codebase", "codetype", "color", "cols", "colspan", "compact", "content", "contenteditable", "contextmenu", "coords", "datetime", "declare", "defer", "dir", "disabled", "draggable", "dropzone", "enctype", "face", "for", "frame", "frameborder", "headers", "height", "hidden", "href", "hreflang", "hspace", "http-equiv", "id", "ismap", "label", "lang", "language", "link", "longdesc", "marginheight", "marginwidth", "maxlength", "media", "method", "multiple", "name", "nohref", "noresize", "noshade", "nowrap", "object", "profile", "prompt", "readonly", "rel", "rev", "rows", "rowspan", "rules", "scheme", "scope", "scrolling", "selected", "shape", "size", "span", "spellcheck", "src", "standby", "start", "style", "summary", "tabindex", "target", "text", "title", "type", "usemap", "valign", "value", "valuetype", "version", "vlink", "vspace", "width");
	$ops = star($ops);
	$validate = true;//(empty($ops['dojoType']) && empty($ops['cellType']) && empty($ops['field']));
	$attributes = "";
	foreach ($ops as $k => $v) if (!is_array($v) && (!$validate || (in_array($k, $valid) || (0===strpos($k, "on")) || (0===strpos($k, "data"))))) $attributes .= " $k=\"$v\"";
	if ($echo) echo $attributes;
	return $attributes;
}
?>
