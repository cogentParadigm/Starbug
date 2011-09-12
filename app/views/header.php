<?php
	include("app/views/functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?php echo Etc::WEBSITE_NAME; ?></title>
		<?php $sb->publish("header"); ?>
	</head>
	<body class="claro">
		<div id="shell" class="container">
			<h1><a href="./"><span><?php echo Etc::WEBSITE_NAME; ?></span></a></h1>
			<span id="subhead"><?php echo Etc::TAGLINE; ?></span>
			<ul id="nav" class="hnav">
				<li><a class="active" href="">Home</a></li>
				<?php if (logged_in()) { ?>
					<li><a href="<?php echo uri("logout"); ?>">Log Out</a></li>
				<?php } else { ?>
					<li><a href="<?php echo uri("login"); ?>">Log In</a></li>
				<?php } ?>
			</ul>
			<div id="main" class="span-24">
