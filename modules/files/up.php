<?php
$this->table("image_styles",
	["name", "type" => "string"],
	["slug", "type" => "string", "default" => "", "slug" => "name"],
	["width", "type" => "int", "default" => "0"],
	["height", "type" => "int", "default" => "0"],
	["adaptive", "type" => "bool", "default" => "1"]
);
$this->table(["files", "list" => "all"],
	["filename", "type" => "string", "length" => "128"],
	["category", "type" => "category", "null" => ""],
	["mime_type", "type" => "string", "length" => "128", "display" => false],
	["size", "type" => "int", "default" => "0", "display" => false],
	["caption", "type" => "string", "length" => "255", "display" => false],
	["styles", "type" => "image_styles", "optional" => ""]
);
$this->table("files_styles",
	["w", "type" => "string", "default" => "0"],
	["h", "type" => "string", "default" => "0"],
	["x", "type" => "string", "default" => "0"],
	["y", "type" => "string", "default" => "0"],
	["cw", "type" => "string", "default" => "0"],
	["ch", "type" => "string", "default" => "0"]
);

//add sortable images to content
$this->column("uris", ["images", "type" => "files", "optional" => ""]);
$this->column("uris_images", ["position", "type" => "int", "ordered" => "", "default" => "0"]);

//add sortable images to content
$this->column("terms", ["images", "type" => "files", "optional" => ""]);
$this->column("terms_images", ["position", "type" => "int", "ordered" => "", "default" => "0"]);

//files category
$this->taxonomy("files_category",
	["term" => "Uncategorized"]
);
?>
