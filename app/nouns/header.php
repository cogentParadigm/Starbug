<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?php echo Etc::WEBSITE_NAME; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo Etc::WEBSITE_URL.Etc::STYLESHEET_DIR."default.css"; ?>" media="screen" />
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.3.2/dijit/themes/tundra/tundra.css"/>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/dojo/1.3.2/dojo/dojo.xd.js"></script>
		<?php $sb->publish("header"); ?>
	</head>
	<body class="tundra">
		<div id="shell">
			<h1><a href="./"><span><?php echo Etc::WEBSITE_NAME; ?></span></a></h1>
			<span id="subhead">Fresh XHTML and CSS, just like mom used to serve!</span>
			<ul id="nav">
				<li><a class="active" href="">Home</a></li>
				<li><a href="">About</a></li>
			</ul>
			<div id="main">
