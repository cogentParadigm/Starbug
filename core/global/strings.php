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
function star($str = array()) {
	if (is_array($str)) return $str;
	$arr = array();
	$keypairs = explode("  ", $str);
	foreach ($keypairs as $keypair) {
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
function P($var) {
	if (substr($var, 0, 1) == "(") return $var;
	return sb()->db->prefix.$var;
}
/**
 * normalize a string
 * @ingroup strings
 * @param string $raw the raw string
 * @param string $valid_chars valid characters. default is 'a-zA-Z0-9'
 * @return string the normalized version of $raw
 */
function normalize($raw, $valid_chars = 'a-zA-Z0-9 \-_') {
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
	foreach ($rules as $pattern => $repl) {
		$plural = preg_replace($pattern, $repl, $singular);
		if ($singular != $plural) break; // leave if plural found
	}
	return $plural;
}
/**
 * helper function to format a machine name into a label
 */
function format_label($name) {
	return ucwords(str_replace('_', ' ', $name));
}
/**
 * convert an array to an HTML attribute string
 * @ingroup strings
 * @param array $ops an associative array
 * @param bool $echo if true then echo out the attribute string (default is true)
 * @return string the HTML attribute string
 */
function html_attributes($ops, $echo = true) {
	$valid = array("abbr", "accept-charset", "accept", "accesskey", "action", "align", "alink", "alt", "archive", "autocomplete", "axis", "background", "bgcolor", "cellpadding", "cellspacing", "char", "charoff", "charset", "checked", "cite", "class", "classid", "clear", "code", "codebase", "codetype", "color", "cols", "colspan", "compact", "content", "contenteditable", "contextmenu", "coords", "datetime", "declare", "defer", "dir", "disabled", "draggable", "dropzone", "enctype", "face", "for", "frame", "frameborder", "headers", "height", "hidden", "href", "hreflang", "hspace", "http-equiv", "id", "ismap", "label", "lang", "language", "link", "longdesc", "marginheight", "marginwidth", "maxlength", "media", "method", "multiple", "name", "nohref", "noresize", "noshade", "nowrap", "object", "placeholder", "profile", "prompt", "readonly", "rel", "rev", "rows", "rowspan", "rules", "scheme", "scope", "scrolling", "selected", "shape", "size", "span", "spellcheck", "src", "standby", "start", "style", "summary", "tabindex", "target", "text", "title", "type", "usemap", "valign", "value", "valuetype", "version", "vlink", "vspace", "width");
	$ops = star($ops);
	$validate = true;//(empty($ops['dojoType']) && empty($ops['cellType']) && empty($ops['field']));
	$attributes = "";
	foreach ($ops as $k => $v) if (!is_array($v) && (!$validate || (in_array($k, $valid) || (0===strpos($k, "on")) || (0===strpos($k, "data"))))) $attributes .= " $k=\"$v\"";
	if ($echo) echo $attributes;
	return $attributes;
}
/**
 * Generates a time elapsed phrase. Supported segments: years, months, weeks, days, hours, minutes, seconds
 * For example, '4 hours' or '3 weeks' or '3 weeks, 1 day, 2 hours, 3 minutes, 10 seconds'
 * @ingroup strings
 * @param string $datetime the date/time string of a past date.
 * @param boolean $full if true, all segments will be included where the value is greater than 0.
 * @return string the time elapsed phrase
 */
function time_elapsed_string($datetime, $full = false) {
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second'
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . t($v . ($diff->$k > 1 ? 's' : ''));
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) : 'just now';
}
/**
 * XSS filter functions
 */
function filter_boolean($boolean) {
	return filter_var($boolean, FILTER_VALIDATE_BOOLEAN);
}
function filter_int($int) {
	return filter_var($int, FILTER_VALIDATE_INT);
}
function filter_float($int) {
	return filter_var($int, FILTER_VALIDATE_FLOAT);
}
function filter_string($string) {
	return preg_replace('/  +/', ' ', strip_tags($string));
}
function filter_url($url) {
	return filter_var($url, FILTER_VALIDATE_URL);
}
function filter_email($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function filter_plain($content) {
	return htmlentities(preg_replace('/  +/', ' ', $content), ENT_QUOTES, 'UTF-8');
}
function filter_html($content, $allowed = array()) {
	$purifier = load_htmlpurifier($allowed);
	return $purifier->purify($content);
}
function load_htmlpurifier($allowed = array()) {
	import("htmlpurifier");
	if (empty($allowed)) {
		$allowed = array(
			'img[src|alt|title|width|height|style|data-mce-src|data-mce-json]',
			'figure', 'figcaption',
			'video[src|type|width|height|poster|preload|controls]', 'source[src|type]',
			'a[href|target]',
			'iframe[width|height|src|frameborder|allowfullscreen]',
			'strong', 'b', 'i', 'u', 'em', 'br', 'font',
			'h1[style]', 'h2[style]', 'h3[style]', 'h4[style]', 'h5[style]', 'h6[style]',
			'p[style]', 'div[style]', 'center', 'address[style]',
			'span[style]', 'pre[style]',
			'ul', 'ol', 'li',
			'table[width|height|border|style]', 'th[width|height|border|style]',
			'tr[width|height|border|style]', 'td[width|height|border|style]',
			'hr'
		);
	}
	$config = HTMLPurifier_Config::createDefault();
	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
	$config->set('CSS.AllowTricky', true);
	$config->set('Cache.SerializerPath', '/tmp');

	// Allow iframes from:
	// o YouTube.com
	// o Vimeo.com
	$config->set('HTML.SafeIframe', true);
	$config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');

	$config->set('HTML.Allowed', implode(',', $allowed));

	// Set some HTML5 properties
	$config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
	$config->set('HTML.DefinitionRev', 1);

	if ($def = $config->maybeGetRawHTMLDefinition()) {
		// http://developers.whatwg.org/sections.html
		$def->addElement('section', 'Block', 'Flow', 'Common');
		$def->addElement('nav', 'Block', 'Flow', 'Common');
		$def->addElement('article', 'Block', 'Flow', 'Common');
		$def->addElement('aside', 'Block', 'Flow', 'Common');
		$def->addElement('header', 'Block', 'Flow', 'Common');
		$def->addElement('footer', 'Block', 'Flow', 'Common');

		// Content model actually excludes several tags, not modelled here
		$def->addElement('address', 'Block', 'Flow', 'Common');
		$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

		// http://developers.whatwg.org/grouping-content.html
		$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
		$def->addElement('figcaption', 'Inline', 'Flow', 'Common');

		// http://developers.whatwg.org/the-video-element.html#the-video-element
		$def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
			'src' => 'URI',
			'type' => 'Text',
			'width' => 'Length',
			'height' => 'Length',
			'poster' => 'URI',
			'preload' => 'Enum#auto,metadata,none',
			'controls' => 'Bool',
		));
		$def->addElement('source', 'Block', 'Flow', 'Common', array(
			'src' => 'URI',
			'type' => 'Text',
		));

		// http://developers.whatwg.org/text-level-semantics.html
		$def->addElement('s', 'Inline', 'Inline', 'Common');
		$def->addElement('var', 'Inline', 'Inline', 'Common');
		$def->addElement('sub', 'Inline', 'Inline', 'Common');
		$def->addElement('sup', 'Inline', 'Inline', 'Common');
		$def->addElement('mark', 'Inline', 'Inline', 'Common');
		$def->addElement('wbr', 'Inline', 'Empty', 'Core');

		// http://developers.whatwg.org/edits.html
		$def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
		$def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));

		// TinyMCE
		$def->addAttribute('img', 'data-mce-src', 'Text');
		$def->addAttribute('img', 'data-mce-json', 'Text');

		// Others
		$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
		$def->addAttribute('table', 'height', 'Text');
		$def->addAttribute('td', 'border', 'Text');
		$def->addAttribute('th', 'border', 'Text');
		$def->addAttribute('tr', 'width', 'Text');
		$def->addAttribute('tr', 'height', 'Text');
		$def->addAttribute('tr', 'border', 'Text');
	}

	return new HTMLPurifier($config);
}
?>
