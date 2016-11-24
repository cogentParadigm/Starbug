<?php
use Starbug\Core\URL;
use Starbug\Core\Request;
// include init file
include("core/init.php");

$request = new Request(URL::createFromSuperGlobals($container->get("website_url")));
$request->setHeaders($_SERVER)
				->setPost($_POST)
				->setFiles($_FILES)
				->setCookies($_COOKIE);

$path = $request->getPath();
if (empty($path)) {
	$request->setPath($container->get("default_path"));
}

$container->set("Starbug\Core\RequestInterface", $request);
$application = $container->get("Starbug\Core\ApplicationInterface");
$response = $application->handle($request);
$response->send();
?>
