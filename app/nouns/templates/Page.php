<?php
$name = current($this->uri);
$pg = $sb->query("pages", "where:name='$name'	limit:1");
include("app/nouns/layouts/$pg[layout].php");
?>
