<?php
	$content = $_REQUEST['content'];
	$type = $_REQUEST['type'];
	$filename = uniqid(rand());
	$filepath = BASE_DIR."/var/tmp/$filename";
	$file = fopen($filepath, "wb");
	fwrite($file, $content);
	fclose($file);
	$output = array();
	if ($type == "php") {
		exec("php -d display_errors=1 -l $filepath", $output);
		unlink($filepath);
		array_pop($output);
		array_shift($output);
		$search = array("Parse error: ", " in $filepath", "syntax error, ");
		$replace = array("", "", "");
		foreach ($output as $i => $o) $output[$i] = str_replace($search, $replace, $o);
	} else if ($type == "javascript") {
		$line = str_replace("jsl:", "", exec("whereis jsl"));
		if (!empty($line)) {
			exec("jsl -process $filepath -nocontext -nosummary -nofilelisting -nologo -output-format '__ERROR__ on line __LINE__'", $output);
			foreach($output as $i => $o) if (false === strpos($o, "SyntaxError")) unset($output[$i]);
			foreach($output as $i => $o) $output[$i] = str_replace("SyntaxError: ", "", $o);
		}
	}
	if (!empty($output)) echo "<span class=\"error\">".implode("\n", $output)."</span>";
?>