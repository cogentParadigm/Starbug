<?php
use Starbug\Http\Url;
use Starbug\Http\Request;

// include init file
include("core/init.php");

$request = new Request(Url::createFromSuperGlobals($container->get("website_url")));
$request->setHeaders($_SERVER)
  ->setPost($_POST)
  ->setFiles($_FILES)
  ->setCookies($_COOKIE);

$path = $request->getPath();
if (empty($path)) {
  $request->setPath($container->get("default_path"));
}

$container->set("Starbug\Http\RequestInterface", $request);
$application = $container->get("Starbug\Core\ApplicationInterface");
$response = $application->handle($request);
$response->send();
?>
