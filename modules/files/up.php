<?php
$this->table("files  list:all",
	"mime_type  type:string  length:128",
	"filename  type:string  length:128",
	"category  type:category  null:",
	"caption  type:string  length:255",
	"directory  type:int  default:1  display:false"
);

//add file attachments to terms
$this->column("terms", "attachment  type:int  upload:term_attachment  null:  references:files id");
?>
