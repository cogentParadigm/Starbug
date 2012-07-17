<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Module.php
 * module
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
$sb->provide("core/lib/Module");
/**
 * module class
 * used for installation/uninstallation to handle management of dependencies and cache
 * @ingroup core
 */
class Module {

	var $dir;
	var $info;
	var $index;
	var $requires = array();
	var $provides = array();
	var $errors = array();
	var $deps = array();
	var $max_length = 100;

	/**
	 * constructor. fetches module data 
	 */
	function __construct($dir) {
		//module name
		$this->dir = $dir;
		//module info
		$this->info = module($this->dir);
		foreach ($this->info as $k => $v) $this->{$k} = $v;
		//module index
		$this->index = get_module_index();
		//required modules
		if (!is_array($this->requires)) $this->requires = array($this->requires);
		foreach ($this->requires as $req) {
			$this->deps[$req] = isset($this->index[$req]);
			if (!$this->deps[$req]) $this->errors['deps'] = "Missing dependency $req";
		}
		//provided modules
		if (!is_array($this->provides)) $this->provides = array($this->provides);
		$this->provides[] = $this->dir;
		foreach ($this->provides as $provided) if (isset($this->index[$provided]) && $this->index[$provided] != $this->dir) $this->errors[] = "$provided already provided by ".$this->index[$provided];
	}

	/**
	 * outputs dependency check and enables a module or outputs errors
	 */
	function install() {
		if (isset($this->index[$this->dir]) && $this->index[$this->dir] == $this->dir) echo "re-";
		echo "installing module ".$this->name."..\n";
		if (!empty($this->requires)) {
			echo "\tdependencies:\n";
			foreach ($this->requires as $d) {
				$length = strlen($d);
				echo str_pad("\t".substr($d, 0, 100), max($this->max_length, $length) - $length)." .. ";
				echo ($this->deps[$d]) ? "\033[32mOK\033[0m" : "\033[31mNO\033[0m";
				echo "\n";
			}
		}
		if (!empty($this->errors)) echo implode("\n", $this->errors)."\n";
		else {
			$this->enable();
			echo "Done!\n";
		}
	}
	
	/**
	 * enable or re-cache the module
	 */
	function enable() {
		//add module
		if (!isset($this->index[$this->dir])) config("modules.", $this->dir);
		//update cache
		$this->cache_directory();
	}
	
	/**
	 * update cached paths of files in the module
	 */
	function cache_directory($path="", $prefix="", $themes=array()) {
		if (!empty($path)) $prefix .= "/";
		if (empty($themes)) $themes = config("themes");
		$handle = opendir(BASE_DIR."/modules/".$this->dir.$prefix.$path);
		if (!$handle) return;
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($file)) $this->cache_directory($file, $prefix.$path, $themes);
				else {
					foreach ($themes as $theme) {
						cache_delete($theme."_".ltrim($prefix.$path, '/')."/".$file);
					}
				}
			}
		}
	}

}
