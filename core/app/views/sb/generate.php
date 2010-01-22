<?php
include("etc/Theme.php");
$themedir = "app/themes/".Theme::FOLDER."/";
$header = "";
$page=next($this->uri);
if (empty($page)) $page = "list";
if (file_exists($themedir."$page.php")) {
	$page = $themedir."$page.php";
	if (!empty($_POST['generate'])) {
		include($themedir."ThemeFunctions.php");
		ThemeFunctions::$_POST['generate']();
	}
	$header = "<h2>Generate</h2>\n";
} else {
	header("HTTP/1.1 404 Not Found");
	$page = "core/app/views/missing.php";
}
include("core/app/views/header.php");
echo $header;
include("core/app/views/include/toolnav.php");
include($page);
include("core/app/views/footer.php");
?>
