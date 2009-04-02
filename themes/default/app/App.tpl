<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="keywords" content="Starbug, Website, Web Site, Web Development, Engine, Framework" />
		<meta name="description" content="Built on the Starbug PHP Development Engine. A code and content management engine for PHP developers." />
		<link rel="stylesheet" type="text/css" href="<?php echo Etc::WEBSITE_URL."core/".Etc::STYLESHEET_DIR."default.css"; ?>" media="screen" />
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.2.3/dijit/themes/tundra/tundra.css"/>
		<!--[if lt IE 8]><script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE8.js" type="text/javascript"></script><![endif]-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.2.3/dojo/dojo.xd.js"></script>
		<title><?php echo Etc::WEBSITE_NAME; ?></title>
	</head>
	<body>
		<div id="shell">
			<h1><a href="./"><span>Starbug</span></a></h1>
			<span id="subhead">PHP meta content manager</span>
			<?php $page = current($this->uri); if (file_exists("app/nouns/".$page.".php")) include("app/nouns/".$page.".php"); ?>
			<ul id="footer">
				<li><a href="http://www.starbugphp.com">StarbugPHP WSDK</a> &copy; 2008-2009 <a href="http://www.aligangji.com">Ali Gangji</a></li>
				<li><a href="http://www.starbugphp.com/freedoms">freedoms</a></li>
			</ul>
		</div>
	</body>
</html>