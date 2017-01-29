<?php
return [
	'theme' => 'starbug-1',
	'Starbug\Css\CssLoader' => DI\object()
		->constructorParameter('modules', DI\get('modules')),
	'Starbug\Css\RouteFilter' => DI\object()->constructorParameter('theme', DI\get('theme')),
	'Starbug\Css\CssBuildCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
	'Starbug\Core\Routing\RouterInterface' => DI\object()
		->method('addFilter', DI\get('Starbug\Css\RouteFilter')),
];
