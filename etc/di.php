<?php
use \Interop\Container\ContainerInterface;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\PHPConsoleHandler;
use \Etc;
return array(
	'environment' => Etc::ENVIRONMENT,
	'base_directory' => BASE_DIR,
	'modules' => array(
		"core" => "core/app",
		"db" => "modules/db",
		"users" => "modules/users",
		"emails" => "modules/emails",
		"files" => "modules/files",
		"comments" => "modules/comments",
		"css" => "modules/css",
		"js" => "modules/js",
		"theme" => "app/themes/starbug-1",
		"var" => "var",
		"app" => "app"
	),
	'database_name' => DEFAULT_DATABASE,
	'log.handlers.development' => [
		DI\get('Monolog\Handler\StreamHandler'),
		DI\get('Monolog\Handler\PHPConsoleHandler')
	],
	'log.handlers.production' => [
		DI\get('Monolog\Handler\StreamHandler')
	],
	'Monolog\Handler\StreamHandler' => function(ContainerInterface $c) {
		$name = $c->get("environment");
		$handler = new StreamHandler('var/log/'.$name.".log");
		return $handler;
	},
	'Monolog\Handler\PHPConsoleHandler' => function(ContainerInterface $c) {
		PhpConsole\Connector::setPostponeStorage(new PhpConsole\Storage\File('/tmp/pc.data'));
		return new Monolog\Handler\PHPConsoleHandler();
	}
);
?>
