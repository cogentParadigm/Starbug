<?php
	$ignores = array("/^\.$/");
	$uri = uri("rogue");
	$mydir = getcwd();
	$browse = $_REQUEST['browse'];
	if (empty($browse)) $browse = ".";
	if($browse == ".") {
		$ignores[] = "/^\.\.$/";
	}
	$gitignore = trim(file_get_contents(BASE_DIR."/.gitignore"));
	$search = array("\n", "*");
	$replace = array("/\n/", ".*");
	$ignores = array_merge($ignores, explode("\n", "/".str_replace($search, $replace, $gitignore)."/"));
?>
<?php
	// Security Check
	$ohno = strstr($browse, "..") || (!strstr(realpath($browse), $mydir)) || strstr($open, "..");
	if($ohno) exit("It is pitch black. You are likely to be eaten by a grue.");
?>
<?php
	$numfiles = 0; $numdirs = 0;
	chdir($browse);
	if ($handle = opendir(".")) {
		$cwd = getcwd();
		$cwd = str_replace($mydir, "", $cwd);
		$cwd = trim($cwd, "/");
		$breadcrumb = BuildBreadcrumb($cwd);
		echo "<span style=\"padding:0px 5px\">$breadcrumb</span><br />";

		$files = array(); $dirs = array();
		while (false !== ($file = readdir($handle))) {
 			$ignore = false; foreach ($ignores as $i) if (preg_match($i, $file)) $ignore = true;
			//if (!in_array($file, $ignores)) {
			if (!$ignore) {
				if(!is_dir("$file")) $files[] = $file;
				else $dirs[] = $file;
			}
		}

		sort($files); sort($dirs);
		$files = array_merge($dirs, $files);
		closedir($handle);
		if ($files) {
			echo "<table id='explorer'>";
			foreach ($files as $file) {
				if ($file == "..") {
					$parts = explode("/", $browse); array_pop($parts); $filepath = implode("/", $parts);
				} else $filepath = "$browse/$file";
				$filepath = str_replace("./", "", $filepath);
				echo "<tr>";
				if(is_dir("$file")){
					echo "<td class='filename'>[ <a href=\"javascript:ide.browseTo('$filepath');\">$file</a> ]</td>";
					echo "<td class='fileinfo'> folder </td>";
					$numdirs+=1;
				} else {
					$size = filesize("$file");
					echo "<td class='filename'><a href=\"javascript:ide.openFile('$browse/$file', '$file');\">$file</a></td> ";
					if ($size < 1024) echo "<td class='fileinfo'> $size bytes </td>" ;
					else if ($size < 10240) echo "<td class='fileinfo'> ".number_format($size/1024, 2)." KiB </td>" ;
					else echo "<td class='fileinfo'> ".number_format($size/1024)." KiB </td>" ;
					$numfiles+=1;
				}
				echo "</tr>";
			}
			echo "</table>";
		}
	}
	echo "<b style=\"padding-left:5px\">$numfiles file".(($numfiles==1)?"":"s");
	echo " and $numdirs folder".(($numdirs==1)?"":"s")."</b>";

/// Functions ///
function BuildBreadcrumb($path) {
		global $browse_cmd, $script_root;
		$breadcrumb = "";
		if($path == ""){
			$breadcrumb = "/";
		} else {
			$a = explode("/", $path);
			foreach($a as $v){
				$breadcrumb = $breadcrumb.($breadcrumb==""?"":"/").$v;
				$paths[] = $breadcrumb;
			}
			$breadcrumb = "<a href='$script_root'>/</a>";
			for($i=0;$i<count($a);$i++){
				if($i!=count($a)-1)
					$breadcrumb = $breadcrumb." &gt; "."<a href='$browse_cmd$paths[$i]'>$a[$i]</a>";
				else
					$breadcrumb = $breadcrumb." &gt; ".$a[$i];
			}
		}
		return $breadcrumb;
}

function FindFileType($path) {
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
