<?php
return [
	"routes" => DI\add([
		'pages' => ['controller' => 'pages']
	]),
	'db.schema.migrations' => DI\add([
		DI\get('Starbug\Content\Migration')
	]),
	'Starbug\Core\Routing\RouterInterface' => DI\object()
		->method('addAliasStorage', DI\get('Starbug\Content\AliasStorage')),
];
?>
