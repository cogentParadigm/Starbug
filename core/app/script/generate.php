<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP <br/>
 * @file script/generate.php generates code from XSLT stylesheets
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
// IF GENERATING HOST, SKIP RIGHT TO IT AND EXIT
if ($argv[0] == "host") include(dirname(__FILE__)."/generate/host/host.php");
class GenerateCommand {
	public function __construct(Schemer $schemer, ResourceLocatorInterface $locator, ContainerInterface $container, $base_directory) {
		$this->schemer = $schemer;
		$this->locator = $locator;
		$this->base_directory = $base_directory;
		$this->container = $container;
	}
	public function run($argv) {
		$this->schemer->fill();
		$generator = array_shift($argv);
		$model = array_shift($argv);
		// CLI VARS
		$params = array("model" => $model, "generator" => $generator);
		$args = array();
		foreach ($argv as $i => $arg) {
			if (0 === strpos($arg, "-")) {
				$arg = str_replace("-", "", $arg);
				$parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : array($arg, true);
				$args[$parts[0]] = $parts[1];
				$params[$parts[0]] = $parts[1];
			}
		}
		//SET VARS FOR GENERATOR
		$model_name = $model;
		$app_dir = "app/";
		if (!empty($args['module'])) $app_dir = "modules/".$args['module']."/";
		$dirs = array(); $generate = array(); $copy = array();

		//EXPORT LATEST SCHEMER DATA TO XML AND JSON
		if ((!empty($model)) && (isset($this->schemer->tables[$model]))) {
			$data = $this->schemer->get($model);
			if (($generator == "model") || ($generator == "models")) {
				$this->schemer->toXML($data);
				$this->schemer->toJSON($data);
			}
		}
		$template_map = array();
		//LOCATE GENERATOR
		$path = (isset($args['u'])) ? "generate/$generator/update.php" : "generate/$generator/$generator.php";
		if ($result = end($this->locator->locate($path, "script"))) include($result);
		else throw new Exception("Could not find generator '$generator'");
		$render_prefix = reset(explode("/$generator/", str_replace($this->base_directory, "", $result)))."/$generator/";
		$locator = new ResourceLocator($this->base_directory, array($render_prefix));
		$renderer = new Template($locator);

		$class = str_replace(' ', '', ucwords(str_replace("-", " ", $generator))).'GenerateCommand';
		if (class_exists($class)) {
			$command = $this->container->get($class);
			$command->run($params);
			$dirs = $command->dirs;
			$generate = $command->generate;
			$copy = $command->copy;
		}
		//CREATE DIRECTORIES
		foreach ($dirs as $dir) if (!file_exists($this->base_directory."/".$dir)) passthru("mkdir ".$this->base_directory."/$dir");
		//CREATE FILES
		foreach ($generate as $template => $output) {
			if (isset($template_map[$template])) $template = $template_map[$template];
			$o = $this->base_directory."/$output"; //output
			$data = $renderer->capture($template, $params);
			file_put_contents($o, $data);
		}
		//COPY FILES
		foreach ($copy as $origin => $dest) passthru("cp ".$this->base_directory."/$dest ".$this->base_directory."/script/generate/$origin");

	}
	public function help() {
		$help = "Usage: generate TYPE NAME [OPTIONS]\n\n";
		$help .= "TYPE\tWhat to generate\n";
		$help .= "    \t\tcrud - CRUD views\n";
		$help .= "    \t\tmodel - Object model\n";
		return $help;
	}
}
?>
