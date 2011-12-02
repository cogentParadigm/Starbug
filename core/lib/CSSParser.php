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
	function __construct($filename, $output_path="") {
		$this->css = array();
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
		$this->css[$desc] = str_replace("url(../", "url(../../../app/public/", $this->optimize(file_get_contents($filename)));
	}
	function optimize($sheet) {
		$sheet = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sheet);
		$sheet = str_replace("\n", "", $sheet);
		$search = array("/{\s+/", "/\s+{/", "/;\s+/", "/:\s+/", "/}\s+/", "/;}/", "/\s+}/", "/\('/", '/\("/', "/'\)/", '/"\)/');
		$replace = array("{", "{", ";", ":", "}", "}", "}", "(", "(", ")", ")");
		$sheet = preg_replace($search, $replace, $sheet);
		$search = array("}", "/*", "*/");
		$replace = array("}\n", "\n/*", "*/\n");
		$sheet = str_replace("}", "}\n", $sheet);
		return $sheet;
	}
	function parse() {
		foreach($this->css as $desc => $file) {
			$styles = array();
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
					$p[$prop[0]] = $prop[1];
				}
				$s = trim($s);
				if(isset($styles[$s])) $styles[$s] = array_merge_recursive($styles[$s], $p);
				else $styles[$s] = $p;
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
			foreach ($lines as $s => $rules) {
				$output .= $s."{";
				foreach ($rules as $property => $value) $output .= $property.":".$value.";";
				$output = rtrim($output, ';');
				$output .= "}\n";
			}
		}
		$file = fopen($this->path, "wb");
		fwrite($file, $output);
		fclose($file);
	}
}
?>
