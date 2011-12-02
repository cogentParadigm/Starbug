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
$dirs = array(); $generate = array(); $copy = array();

global $renderer;
$renderer->prefix = "script/generate/$generator/";

//SETUP XML
if ((!empty($model)) && (isset($schemer->tables[$model]))) {
	$data = $schemer->get($model);
	if (($generator == "model") || ($generator == "models")) {
		$schemer->toXML($data);
		$schemer->toJSON($data);
	}
}

//INCLUDE GENERATOR FILE
if (isset($args['u'])) include(BASE_DIR."/script/generate/$generator/update.php");
else include(BASE_DIR."/script/generate/$generator/$generator.php");

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
