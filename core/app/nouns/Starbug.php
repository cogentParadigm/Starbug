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
			<h1><a href="<?php echo Etc::WEBSITE_URL; ?>"><span>Starbug</span></a></h1>
			<span id="subhead">PHP meta content manager</span>
			<?php if ($this->payload['security'] > $_SESSION[P('security')]) include("core/app/nouns/login.php");
			else { $page = current($this->uri); if (file_exists("core/app/nouns/".$page.".php")) include("core/app/nouns/".$page.".php"); }?>
			<ul id="footer">
				<li><a href="http://www.starbugphp.com">StarbugPHP WSDK</a> &copy; 2008-2009 <a href="http://www.aligangji.com">Ali Gangji</a></li>
				<li><a href="http://www.starbugphp.com/freedoms">freedoms</a></li>
			</ul>
		</div>
		<?php if ($_SESSION[P('security')] >= Etc::ADMIN_SECURITY) { ?>
		<div id="dash">
			<script type="text/javascript">
				function add_uri() {
					dojo.xhrGet({
						url: '<?php echo uri("uris/add"); ?>',
						load: function (data) {
							dojo.byId('dash_form').innerHTML += data;
						}
					});
				}
				function save_add() {
					var loader = dojo.byId('loading');
					dojo.style(loader, {visibility:"visible"});
					dojo.xhrPost({
						url: '<?php echo uri("uris/add"); ?>',
						form: 'add_uri_form',
						load: function(data) {
							cancel_add();
						}
					});
				}
				function cancel_add() {
					var newrow = dojo.byId('add_uri');
					newrow.parentNode.removeChild(newrow);
					var loader = dojo.byId('loading');
					dojo.style(loader, {visibility:"hidden"});
				}
			</script>
			<div id="dash_form"></div>
			<ul id="dashlist">
				<li class="first"><a href="<?php echo uri("models"); ?>">model</a></li>
				<li><a href="<?php echo uri("generate"); ?>">generate</a></li>
				<!--<li><a class="add" href="uris/create" onclick="add_uri();return false;">+</a><a href="<?php echo uri("uris"); ?>">uris</a></li>
				<li><a class="add" href="users/create" onclick="add_user();return false;">+</a><a href="<?php echo uri("users"); ?>">users</a></li>
				<li id="loading" style="visibility:hidden"></li>-->
			</ul>
		</div>
		<?php } ?>
	</body>
</html>
