<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="keywords" content="Starbug, Website, Web Site, Web Development, Engine, Framework" />
		<meta name="description" content="Built on the Starbug PHP Development Engine. A code and content management engine for PHP developers." />
		<link rel="stylesheet" type="text/css" href="core<?php echo Etc::STYLESHEET_DIR."default.css"; ?>" media="screen" />
		<!--[if lt IE 7.]>
		<script defer type="text/javascript" src="public/js/pngfix.js"></script>
		<![endif]-->
		<script type="text/javascript" src="public/js/jquery.min.js"></script>
		<title><?php echo Etc::WEBSITE_NAME; ?></title>
	</head>
	<body>
		<div id="shell">
			<h1><a href="./"><span>Starbug</span></a></h1>
			<span id="subhead">PHP Request Engine</span>
			<?php $page = dfault($page, "Home"); if (file_exists("core/app/elements/".$page.".php")) include("core/app/elements/".$page.".php"); ?>
		</div>
	</body>
</html>