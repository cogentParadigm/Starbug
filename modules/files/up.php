<?php
$this->table("files  list:all",
	"filename  type:string  length:128",
	"category  type:category  null:",
	"mime_type  type:string  length:128  display:false",
	"size  type:int  default:0  display:false",
	"caption  type:string  length:255  display:false"
);

//add sortable images to content
$this->column("uris", "images  type:files  optional:");
$this->column("uris_images", "position  type:int  ordered:  default:0");

//add file attachments to terms
$this->column("terms", "attachments  type:files");

//files category
$this->taxonomy("files_category",
	"term:Uncategorized"
);
?>
