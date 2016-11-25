<?php
namespace Starbug\Css;
class CssParser {
	public $path;
	public $css;
	public $fonts;
	function __construct($filename, $output_path = "") {
		$this->css = $this->fonts = array();
		$args = func_get_args();
		$count = count($args);
		if ($count == 1) {
			//IF THERE IS ONLY ONE PARAM, USE IT AS THE OUTPUT PATH
			$this->path = $args[0];
		} else {
			//IF THERE ARE MORE PARAMS, USE THE LAST ONE AS THE OUTPUT PATH
			$this->path = array_pop($args);
			foreach ($args as $a) $this->add_file($a);
		}
	}
	function add_file($filename, $desc = "") {
		if (empty($desc)) $desc = end(explode("/", $filename));
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
		$search = array("/{\s+/", "/\s+{/", "/;\s+/", "/:\s+/", "/}\s+/", "/;}/", "/\s+}/",'/\("/', '/"\)/');
		$replace = array("{", "{", ";", ":", "}", "}", "}", "(", ")");
		$sheet = preg_replace($search, $replace, $sheet);
		preg_match_all("/@import url\(\"([^\)]*)\"\)/", $sheet, $matches);
		$sheet = preg_replace("/@import url\([^\)]*\);/", "", $sheet);
		if (!empty($matches[0])) {
			foreach ($matches[1] as $match) $this->add_file(realpath($dir."/".$match));
		}
		return $sheet;
	}
	function write() {
		foreach ($this->css as $desc => $css) $this->css[$desc] = "/* $desc */".$css;
		$file = fopen($this->path, "wb");
		fwrite($file, implode("\n", $this->css));
		fclose($file);
	}
}
?>
