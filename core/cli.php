<?php
$args = ['cli' => true];
foreach ($argv as $i => $arg) {
  if (0 === strpos($arg, "-")) {
    $arg = str_replace("-", "", $arg);
    $parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : [$arg, true];
    $args[$parts[0]] = $parts[1];
  }
}

include("init.php");

$url = $container->make("Starbug\Core\URL", ['base_directory' => $container->get("website_url")]);
$request = $container->make("Starbug\Core\Request", ['url' => $url]);
$request->setHeaders($_SERVER);
$container->set("Starbug\Core\RequestInterface", $request);
$user = $container->get("Starbug\Core\IdentityInterface");
$user->setUser(["id" => "NULL", "groups" => ["root"]]);
