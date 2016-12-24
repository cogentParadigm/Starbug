<?php
if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/script", "", dirname(__FILE__)));
if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));

//CREATE FOLDERS & SET FILE PERMISSIONS
$dirs = array("var", "var/xml", "var/json", "var/models", "var/tmp", "var/public", "var/log", "var/public/stylesheets", "var/public/thumbnails", "app/templates", "app/public/js", "app/public/uploads");
foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) exec("mkdir ".BASE_DIR."/".$dir);
exec("chmod -R a+w ".BASE_DIR."/var ".BASE_DIR."/app/public/uploads");

//INIT TABLES
$db = $container->get("Starbug\Core\DatabaseInterface");
$schemer = $container->get("Starbug\Db\Schema\SchemerInterface");
$schemer->migrate();

$root_user = $db->query("users")->condition("email", "root")->one();
if (empty($root_user['password']) || $root_user['modified'] === $root_user['created']) { // PASSWORD HAS NEVER BEEN UPDATED
	//COLLECT USER INPUT
	fwrite(STDOUT, "\nPlease choose a root password:");
	$admin_pass = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "\n\nYou may log in with these credentials -");
	fwrite(STDOUT, "\nusername: root");
	fwrite(STDOUT, "\npassword: $admin_pass\n\n");
	//UPDATE PASSWORD
	$errors = $db->store("users", array("id" => $root_user["id"], "password" => $admin_pass, "groups" => "root,admin"));
}
