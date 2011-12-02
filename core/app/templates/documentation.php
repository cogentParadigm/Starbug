<?php
	$path = ltrim($request->path, "documentation");
	efault($path, "/index");
	efault($request->format, "html");
	if ("css" == $request->format) $mime = "text/css";
	else if ("js" == $request->format) $mime = "text/javascript";
	else if ("png" == $request->format) $mime = "image/png";
	else if (("jpg" == $request->format) || ("jpeg" == $request->format)) $mime = "image/jpeg";
	else if ("gif" == $request->format) $mime = "image/gif";
	else $mime = "text/".$request->format;
	header("Content-type: $mime");
	include("core/app/views/documentation".$path.".".$request->format);
?>
