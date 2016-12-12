<?php
return [
	'Starbug\Js\DojoConfiguration' => DI\object()
		->constructorParameter('environment', DI\get('environment')),
	'Starbug\Js\DojoBuildCommand' => DI\object()
		->constructorParameter('base_directory', DI\get('base_directory'))
];
