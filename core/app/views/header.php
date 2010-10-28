<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="<?php echo uri("core/".Etc::STYLESHEET_DIR."default.css"); ?>" media="screen" />
		<script type="text/javascript" src="http://o.aolcdn.com/dojo/1.3/dojo/dojo.xd.js" djConfig="parseOnLoad: true"></script>
		<title><?php echo Etc::WEBSITE_NAME; ?></title>
	</head>
	<body class="tundra<?php if (!empty($body_class)) echo " ".$body_class; ?>">
		<div id="shell">
			<h1><a href="<?php echo uri(); ?>"><span><?php echo Etc::WEBSITE_NAME; ?></span></a></h1>
			<span id="subhead"><?php echo Etc::TAGLINE; ?></span>
