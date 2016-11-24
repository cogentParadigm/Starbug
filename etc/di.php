<?php
use \Interop\Container\ContainerInterface;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\PHPConsoleHandler;
return array(
	'modules' => array(
		"Starbug\Core" => "core/app",
		"Starbug\Db" => "modules/db",
		"Starbug\Doctrine" => "modules/doctrine",
		"Starbug\Users" => "modules/users",
		"Starbug\Files" => "modules/files",
		"Starbug\Emails" => "modules/emails",
		"Starbug\Comments" => "modules/comments",
		"Starbug\Css" => "modules/css",
		"Starbug\Js" => "modules/js",
		"Starbug\Content" => "modules/content",
		"Starbug\Theme" => "app/themes/starbug-1",
		"Starbug\Var" => "var",
		"Starbug\App" => "app"
	),
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
