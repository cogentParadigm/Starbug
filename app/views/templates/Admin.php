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
		<div id="shell">
			<div id="top-right">
				<a href="<?php echo uri("logout"); ?>" class="small right blue button" style="margin:2px 0 0 5px"><span>Logout</span></a>
				<a href="#"><?php echo userinfo("username"); ?></a>
			</div>
			<h1><a href="./"><span><?php echo Etc::WEBSITE_NAME; ?></span></a></h1>
			<span id="subhead"><?php echo Etc::TAGLINE; ?></span>
			<ul id="tabs" class="hnav">
					<li><a class="button" href="<?php echo uri("profile"); ?>"><span>Profile</span></a></li>
			</ul>
			<ul id="nav" class="hnav">
				<li class="first"><a href="<?php echo uri("admin"); ?>">Dashboard</a></li>
			</ul>
			<div id="main">
				<?php include($this->file); ?>
				<ul id="footer" class="small right clear">
					<li>Powered by <a href="http://www.starbugphp.com">Starbug PHP</a></li>
				</ul>
			</div>
		</div>
		<?php $sb->publish("footer"); ?>
	</body>
</html>
