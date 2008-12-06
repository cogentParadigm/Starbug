<?php
function load_file($location) {if (file_exists($location.".php") && require_once($location.".php")) return true; else return false;}
load_file("core/db/adodb_lite/adodb.inc");
$db = ADONewConnection('mysql');
$db->Connect(Etc::DB_HOST, Etc::DB_USERNAME, Etc::DB_PASSWORD, Etc::DB_NAME);
function empty_nan($val, $default="") {if(empty($val) || !is_numeric($val)) return $default; else return $val;}
function dfault($val, $default="") {if(empty($val)) return $default; else return $val;}
function A($str="") {return Starr::star($str);}
function rA($str="") {return Starr::rstar($str);}
function D_exists($obj) {return file_exists("app/models/".$obj.".php");}
function D($obj, $data) {if (include_once("app/models/".$obj)) $obj = new $obj($data, strtolower($obj)); else return false; return $obj;}
function T($name, $content, $attrs="") {return HT::tag($name, $content, $attrs);}
function L($type, $content, $attrs="") {return HT::lis($type, $content, $attrs);}
function F($type, $args, $act="") {if (($type=="post") || ($type=="get")) return Form::render($args, $type);}
function P($var) {return Etc::PREFIX.$var;}
function R($arg1) {$args = func_get_args(); return implode("\n", $args);}
?>
