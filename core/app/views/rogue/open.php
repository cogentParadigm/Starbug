<?php
	$qtype = $_REQUEST["type"];
	$download = $_REQUEST["download"];
	$open = $_REQUEST['open'];
	$id = $_REQUEST['id'];

if ($open) {

	if($download) {
	
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($open));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($open));
		ob_clean();
		flush();
		readfile($open);
		exit;
	
	} else {

		$type = ($qtype) ? $qtype : FindFileType($open);
		echo "<select id=\"$id"."_type\" onchange=\"ide.setType('".str_replace("tab", "", $id)."');\" style=\"margin-right:5px;float:left\">";
		echo "<option value=\"PHPHTMLMixedParser\"".(($type == "php") ? " selected=\"selected\"" : "").">PHP</option>";
		echo "<option value=\"JSParser\"".(($type == "javascript") ? " selected=\"selected\"" : "").">JS</option>";
		echo "<option value=\"CSSParser\"".(($type == "css") ? " selected=\"selected\"" : "").">CSS</option>";
		echo "<option value=\"HTMLMixedParser\"".(($type == "html") ? " selected=\"selected\"" : "").">HTML</option>";
		echo "<option value=\"SQLParser\"".(($type == "sql") ? " selected=\"selected\"" : "").">SQL</option>";
		echo "</select><div class=\"left\" style=\"line-height:30px\">$open</div><div class=\"left alerts\" style=\"margin-left:5px\"></div><br/><br/><br/>";
		echo "<textarea id=\"$id\" type=\"$type\">";
		echo htmlentities(file_get_contents($open));
		echo "</textarea>";

	}
}

function FindFileType($path){
	// $types["ext"] = "type";
	$types["c"] = "cpp"; 
	$types["cpp"] = "cpp"; 
	$types["cs"] = "csharp"; 
	$types["css"] = "css"; 
	$types["html"] = "html";
	$types["htm"] = "html";
	$types["java"] = "java"; 
	$types["js"] = "javascript"; 
	$types["pl"] = "perl"; 
	$types["rb"] = "ruby";	
	$types["php"] = "php"; 
	$types["txt"] = "plain"; 
	$types["sql"] = "sql";
	$types["vb"] = "vb";
	$types["xml"] = "xml";
	
	$path = strtolower($path) ;
	$ext = split("[/\\.]", $path) ;
	$n = count($ext)-1;
	$ext = $ext[$n];
	
	$ext = $types[$ext];	
	if(!$ext)$ext = "plain";
	
	return $ext;
}

?>