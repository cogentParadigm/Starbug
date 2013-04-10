<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP <br/>
 * @file script/generate.php generates code from XSLT stylesheets
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
global $sb;
// USAGE TEXT
$help = "Usage: generate TYPE NAME [OPTIONS]\n\n";
$help .= "TYPE\tWhat to generate\n";
$help .= "    \t\tcrud - CRUD views\n";
$help .= "    \t\tmodel - Object model\n";

$generator = array_shift($argv);
$model = array_shift($argv);

// IF GENERATING HOST, SKIP RIGHT TO IT AND EXIT
if ($generator == "host") include(dirname(__FILE__)."/generate/host/host.php");

// CLI VARS
assign("model", $model);
assign("generator", $generator);
$args = array();
foreach ($argv as $i => $arg) {
	if (0 === strpos($arg, "-")) {
		$arg = str_replace("-", "", $arg);
		$parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : array($arg, true);
		$args[$parts[0]] = $parts[1];
		assign($parts[0], $parts[1]);
	}
}

//SET VARS FOR GENERATOR 
$model_name = $model;
$app_dir = "app/";
if (!empty($args['module'])) $app_dir = "modules/".$args['module']."/";
$dirs = array(); $generate = array(); $copy = array();

global $renderer;

//EXPORT LATEST SCHEMER DATA TO XML AND JSON
if ((!empty($model)) && (isset($schemer->tables[$model]))) {
	$data = $schemer->get($model);
	if (($generator == "model") || ($generator == "models")) {
		$schemer->toXML($data);
		$schemer->toJSON($data);
	}
}

//LOCATE GENERATOR
$path = (isset($args['u'])) ? "generate/$generator/update.php" : "generate/$generator/$generator.php";
if ($result = end(locate($path, "script"))) include($result);
else die("Could not find generator '$generator'");
$renderer->prefix = reset(explode("/$generator/", str_replace(BASE_DIR, "", $result)))."/$generator/";

//CREATE DIRECTORIES
foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) passthru("mkdir ".BASE_DIR."/$dir");
//CREATE FILES
foreach ($generate as $template => $output) {
	$o = BASE_DIR."/$output"; //output
	$data = capture($template);
	file_put_contents($o, $data);
}
//COPY FILES
foreach ($copy as $origin => $dest) passthru("cp ".BASE_DIR."/$dest ".BASE_DIR."/script/generate/$origin");
?>
