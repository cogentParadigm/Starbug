<?php
	$path = ltrim($request->getPath(), "documentation");
	if (empty($path)) $path = "/index";
	if ("css" == $request->getFormat()) $mime = "text/css";
	else if ("js" == $request->getFormat()) $mime = "text/javascript";
	else if ("png" == $request->getFormat()) $mime = "image/png";
	else if (("jpg" == $request->getFormat()) || ("jpeg" == $request->getFormat())) $mime = "image/jpeg";
	else if ("gif" == $request->getFormat()) $mime = "image/gif";
	else $mime = "text/".$request->getFormat();
	header("Content-type: $mime");
	include("core/app/views/documentation".$path.".".$request->getFormat());
?>
