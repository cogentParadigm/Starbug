<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP <br/>
 * @file script/generate.php generates code from XSLT stylesheets
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$help = "Usage: generate TYPE NAME [OPTIONS]\n\n";
$help .= "TYPE\tWhat to generate\n";
$help .= "    \t\tcrud - CRUD nouns\n";
$help .= "    \t\tmodel - Object model\n";
$generator = array_shift($argv);
$model = array_shift($argv);

// IF GENERATING HOST, SKIP RIGHT TO IT AND EXIT
if ($generator == "host") include(dirname(__FILE__)."/generate/host/host.php");

include(BASE_DIR."/util/Args.php");
$args = new Args();
global $sb;
$sb->import("util/XMLBuilder");
//SET VARS FOR GENERATOR 
$model_name = $model;
$dirs = array(); $generate = array(); $copy = array();

//SETUP XML
if ((!empty($model)) && (isset($schemer->tables[$model]))) {
	$fields = $schemer->get_table($model);
	XMLBuilder::write_model($model, $fields);
}

//INCLUDE GENERATOR FILE
if ($args->flag('u')) include(BASE_DIR."/script/generate/$generator/update.php");
else include(BASE_DIR."/script/generate/$generator/$generator.php");

//CREATE DIRECTORIES
foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) passthru("mkdir ".BASE_DIR."/$dir");
//CREATE FILES
foreach ($generate as $stylesheet => $output) {
	$o = BASE_DIR."/$output"; //output
	$s = BASE_DIR."/var/xml/$model.xml"; //source
	$xsl = BASE_DIR."/script/generate/$stylesheet"; //xsl
	passthru(Etc::JAVA_PATH." -jar ".Etc::SAXON_PATH." -o:$o -s:$s -xsl:$xsl 2>&1");
}
//COPY FILES
foreach ($copy as $origin => $dest) passthru("cp ".BASE_DIR."/$dest ".BASE_DIR."/script/generate/$origin");
?>
