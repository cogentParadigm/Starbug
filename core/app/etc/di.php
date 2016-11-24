<?php
use \Interop\Container\ContainerInterface;
use \Monolog\Logger;
return array(
	'environment' => 'development',
	'website_url' => '/',
	'time_zone' => 'America/Vancouver',
	'hmac_key' => '',
	'routes' => [
		"api" => [
			"controller" => "apiRouting",
			"action" => "response"
		],
		"profile" => [
			"title" => "Profile",
			"controller" => "profile"
		],
		"admin" => [
			"title" => "Admin",
			"controller" => "admin",
			"action" => "default_action",
			"groups" => "admin",
			"theme" => "storm"
		],
		"upload" => [
			"title" => "Upload",
			"controller" => "upload",
			"template" => "xhr",
			"groups" => "user"
		],
		"terms" => [
			"template" => "xhr",
			"groups" => "user"
		],
		"robots" => [
			"template" => "txt"
		]
	],
	'Starbug\Core\SettingsInterface' => DI\object('Starbug\Core\DatabaseSettings'),
	'Starbug\Core\*Interface' => DI\object('Starbug\Core\*'),
	'Starbug\Core\ResourceLocator' => DI\object()->constructor(DI\get('base_directory'), DI\get('modules')),
	'Starbug\Core\ModelFactory' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'Starbug\Core\CssGenerateCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'Starbug\Core\ErrorHandler' => DI\object()->constructorParameter("exceptionTemplate", defined('SB_CLI') ? "exception-cli" : "exception-html"),
	'Starbug\Core\SessionStorage' => DI\object()->constructorParameter('key', DI\get('hmac_key')),
	'Starbug\Core\URLInterface' => function(ContainerInterface $c) {
		$request = $c->get("Starbug\Core\RequestInterface");
		return $request->getURL();
	},
	'Starbug\Core\Routing\RouterInterface' => DI\object('Starbug\Core\Routing\Router')
		->method('addStorage', DI\get('Starbug\Core\Routing\MemoryRouteStorage')),
	'Starbug\Core\Routing\*Interface' => DI\object('Starbug\Core\Routing\*'),
	'Starbug\Core\Routing\MemoryRouteStorage' => DI\object()->method('addRoutes', DI\get('routes')),
	'Starbug\Core\Images' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'databases.default' => function(ContainerInterface $c) {
		$config = $c->get("Starbug\Core\ConfigInterface");
		$name = $c->get("database_name");
		$params = $config->get("db/".$name);
		return new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
	},
	'databases.test' => function(ContainerInterface $c) {
		$config = $c->get("Starbug\Core\ConfigInterface");
		$params = $config->get("db/test");
		return new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
	},
	'db.schema.migrations' => [
		DI\get('Starbug\Core\Migration')
	],
	'db.schema.hooks' => [
		DI\get('Starbug\Core\SchemaHook')
	],
	'Starbug\Core\Database' => DI\object()
		->method('setTimeZone', DI\get('time_zone'))
		->method('setDatabase', DI\get('database_name')),
	'Starbug\Core\Template' => DI\object()->constructorParameter('helpers', DI\get('Starbug\Core\HelperFactoryInterface')),
	'Starbug\Core\GenerateCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'Starbug\Core\Application' => DI\object()->method('setLogger', DI\get('Psr\Log\LoggerInterface')),
	'Psr\Log\LoggerInterface' => function(ContainerInterface $c) {
		$logger = new Logger("main");
		$env = $c->get("environment");
		$handlers = $c->get("log.handlers.".$env);
		foreach ($handlers as $handler) {
			$logger->pushHandler($handler);
		}
		return $logger;
	}
);
?>
