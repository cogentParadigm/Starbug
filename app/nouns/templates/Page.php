<?php
$name = current($this->uri);
$pg = $sb->get("page")->get("*", "name='$name'")->fields();
include("app/nouns/layouts/$pg[layout].php");
?>
