<?php
$this->table("files  list:all",
	"filename  type:string  length:128",
	"category  type:category  null:",
	"mime_type  type:string  length:128  display:false",
	"size  type:int  default:0  display:false",
	"caption  type:string  length:255  display:false"
);

//add file attachments to terms
$this->column("terms", "attachment  type:int  upload:term_attachment  null:  references:files id");
?>
