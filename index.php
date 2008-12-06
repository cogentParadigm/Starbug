<?php
//configure
include("etc/Etc.php");
//initialize
include("etc/init.php");
//go
load_file("core/Request");
new Request($db);
?>
