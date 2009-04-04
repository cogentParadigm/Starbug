<?php
$directory=current($this->uri);
$page=next($this->uri);
$page = "core/app/nouns/$directory/$page.php";
include($page);
?>
