<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/CSSParser.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup CSSParser
 */
/**
 * @defgroup CSSParser
 * CSS Parser
 * @ingroup lib
 */
$sb->provide("core/lib/CSSParser");
/**
 * Parses, omptimizes and combines CSS files. Used in conjuction with script/generate/css to replace the blueprint CSS build script.
 * @ingroup CSSParser
 */
class CSSParser {
	var $path;
	var $css;
	var $fonts;
	function __construct($filename, $output_path="") {
		$this->css = $this->fonts = array();
		$args = func_get_args();
		$count = count($args);
		if ($count == 1) { //IF THERE IS ONLY ONE PARAM, USE IT AS THE OUTPUT PATH
			$this->path = $args[0];
		} else { //IF THERE ARE MORE PARAMS, USE THE LAST ONE AS THE OUTPUT PATH
			$this->path = array_pop($args);
			foreach ($args as $a) $this->add_file($a);
		}
	}
	function add_file($filename, $desc="") {
		efault($desc, end(explode("/", $filename)));
		$replacement = str_replace(BASE_DIR, "", realpath(dirname($filename)."/../"))."/"; //translates ../ into ../../../app/public/
		$sheet = str_replace("url(../", "url(../../..$replacement", $this->optimize(file_get_contents($filename), dirname($filename)));
		$base = $desc;
		$count = 0;
		while (!empty($this->css[$desc])) {
			$count++;
			$desc = $base."-".$count;
		}
		$this->css[$desc] = $sheet;
	}
	function optimize($sheet, $dir) {
		$sheet = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sheet);
		$sheet = str_replace("\n", "", $sheet);
		$search = array("/{\s+/", "/\s+{/", "/;\s+/", "/:\s+/", "/}\s+/", "/;}/", "/\s+}/", "/\('/", '/\("/', "/'\)/", '/"\)/');
		$replace = array("{", "{", ";", ":", "}", "}", "}", "(", "(", ")", ")");
		$sheet = preg_replace($search, $replace, $sheet);
		$search = array("}", "/*", "*/");
		$replace = array("}\n", "\n/*", "*/\n");
		$sheet = str_replace("}", "}\n", $sheet);
		preg_match_all("/@import url\(\"?([^\)]*)\"?\)/", $sheet, $matches);
		$sheet = preg_replace("/@import url\([^\)]*\);/", "", $sheet);
		if (!empty($matches[0])) {
			foreach ($matches[1] as $match) $this->add_file(realpath($dir."/".$match));
		}
		return $sheet;
	}
	function parse() {
		foreach($this->css as $desc => $file) {
			$styles = array();
			$fontfaces = array();
			//OPTIMIZED FILE CONTAINS 1 RULESET PER LINE SO FIRST BREAK UP THE LINES
			$lines = explode("\n", rtrim($file, "\n"));
			// NOW WE LOOP THROUGH EACH LINE AND FILL THE STYLES ARRAY
			// array("selectors" => array("property" => "value"))
			foreach($lines as $idx => $line) {
				// SPLIT THE SELECTORS FROM THE RULESET
				$line = explode("{", rtrim($line, '}'));
				// SELECTORS
				$s = $line[0];
				// PROPERTIES
				$props = explode(";", $line[1]);
				$p = array();
				foreach($props as $prop) {
					$prop = explode(":", $prop);
					$p[] = array($prop[0], $prop[1]);
				}
				$s = trim($s);
				if (!empty($s)) {
					$styles[] = array($s, $p);
				}
			}
			$this->css[$desc] = $styles;
		}
	}
	function add_semantic_classes($semantic) {
		$styles = array();
		foreach ($semantic as $selector => $classes) {
			$append = array();
			$classes = explode(" ", $classes);
			foreach($classes as $class) {
				foreach ($this->css as $file) {
					foreach ($file as $sels => $ruleset) {
						$sels = explode(",", $sels);
						foreach ($sels as $s) {
							$s = trim($s);
							if ($class == $s) $append = array_merge($append, $ruleset);
						}
					}
				}
			}
			$styles[$selector] = $append;
		}
		$this->css["semantic class names"] = $styles;
	}
	function add_plugin($plugin) {
		$file = end(explode("/", $this->path));
		if (($file != "print.css") && ($file != "screen.css") && ($file != "ie.css")) $file = "screen.css";
		$filename = BASE_DIR."/core/app/public/stylesheets/plugins/$plugin/$file";
		if (file_exists($filename)) $this->add_file($filename, "$plugin plugin");
	}
	function write() {
		$output = "";
		foreach ($this->css as $desc => $lines) {
			if (!empty($output)) $output .= "\n";
			$output .= "/* $desc */\n";
			foreach ($lines as $idx => $rule) {
					$output .= $rule[0]."{";
					foreach ($rule[1] as $idx => $property) $output .= $property[0].":".$property[1].";";
					$output = rtrim($output, ';');
					$output .= "}";
			}
		}
		$file = fopen($this->path, "wb");
		fwrite($file, $output);
		fclose($file);
	}
}
?>
