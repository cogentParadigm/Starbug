<?php
if (!defined("SB_CLI")) define("SB_CLI", true);

$args = array();
foreach ($argv as $i => $arg) {
	if (0 === strpos($arg, "-")) {
		$arg = str_replace("-", "", $arg);
		$parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : array($arg, true);
		$args[$parts[0]] = $parts[1];
	}
}

include("init.php");

$url = $container->make("Starbug\Core\URL", array('base_directory' => $container->get("website_url")));
$request = $container->make("Starbug\Core\Request", array('url' => $url));
$request->setHeaders($_SERVER);
$container->set("Starbug\Core\RequestInterface", $request);
$user = $container->get("Starbug\Core\IdentityInterface");
$user->setUser(array("id" => "NULL", "groups" => array("root")));
?>
