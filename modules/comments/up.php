<?php
$this->table("comments",
	["name", "type" => "string", "length" => "255"],
	["email", "type" => "string", "length" => "255"],
	["comment", "type" => "text"]
);
//add comments to uris
$this->column("uris", ["comments", "type" => "comments", "display" => "false"]);
?>
