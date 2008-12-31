<?php
define("BASE_DIR", end(split("/",dirname(__FILE__))));
//configure
include("etc/Etc.php");
//initialize
include("etc/init.php");
//go\
include("core/Request.php");
new Request($db);
?>
