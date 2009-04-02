<?php
$directory=current($this->uri);
$page=next($this->uri);
$page = "core/app/nouns/$directory/$page.php";
include("core/app/nouns/header.php");
include($page);
include("core/app/nouns/footer.php");
?>
