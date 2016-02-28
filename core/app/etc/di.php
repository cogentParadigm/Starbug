<?php
use \Interop\Container\ContainerInterface;
use \Monolog\Logger;
return array(
	'environment' => Etc::ENVIRONMENT,
	'database_name' => DEFAULT_DATABASE,
	'Starbug\Core\SettingsInterface' => DI\object('Starbug\Core\DatabaseSettings'),
	'Starbug\Core\*Interface' => DI\object('Starbug\Core\*'),
	'Starbug\Core\ResourceLocator' => DI\object()->constructor(DI\get('base_directory'), DI\get('modules')),
	'Starbug\Core\ModelFactory' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'Starbug\Core\CssGenerateCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'Starbug\Core\ErrorHandler' => DI\object()->constructorParameter("exceptionTemplate", defined('SB_CLI') ? "exception-cli" : "exception-html"),
	'Starbug\Core\SessionStorage' => DI\object()->constructorParameter('key', ETC::HMAC_KEY),
	'Starbug\Core\URLInterface' => function(ContainerInterface $c) {
		$request = $c->get("Starbug\Core\RequestInterface");
		return $request->getURL();
	},
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
	'Starbug\Core\Database' => DI\object()
															->constructorParameter('database_name', DI\get('database_name'))
															->constructorParameter('pdo', DI\get('databases.default'))
															->method('set_debug', Etc::DEBUG),
	'Starbug\Core\Template' => DI\object()->constructorParameter('helpers', DI\get('Starbug\Core\HelperFactoryInterface')),
	'Starbug\Core\Schemer' => DI\object()->constructorParameter('modules', DI\get('modules')),
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
