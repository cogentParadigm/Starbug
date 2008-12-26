<?php
function load_file($location) {return require_once($location.".php");}
include("core/db/adodb_lite/adodb.inc.php");
$db = ADONewConnection('mysql');
$db->Connect(Etc::DB_HOST, Etc::DB_USERNAME, Etc::DB_PASSWORD, Etc::DB_NAME);
function empty_nan(&$val, $default="") {if(empty($val) || !is_numeric($val)) $val = $default;}
function dfault(&$val, $default="") {if(empty($val)) $val = $default;}
function rA($str="") {return Starr::rstar($str);}
function D_exists($obj) {return file_exists("app/models/".$obj.".php");}
function D($obj, $data) {if (include_once("app/models/".$obj)) $obj = new $obj($data, strtolower($obj)); else return false; return $obj;}
function P($var) {return Etc::PREFIX.$var;}
function R($arg1) {$args = func_get_args(); return implode("\n", $args);}
?>
