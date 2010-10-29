<?php
	$path = ltrim($this->path, "documentation");
	efault($path, "/index");
	efault($this->format, "html");
	if ("js" == $this->format) $mime = "text/javascript";
	else if ("png" == $this->format) $mime = "image/png";
	else if (("jpg" == $this->format) || ("jpeg" == $this->format)) $mime = "image/jpeg";
	else if ("gif" == $this->format) $mime = "image/gif";
	else $mime = "text/".$this->format;
	header("Content-type: $mime");
	include("core/app/views/documentation".$path.".".$this->format);
?>
