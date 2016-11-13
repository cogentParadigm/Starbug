<?php
return [
	'Starbug\Css\CssLoader' => DI\object()->constructorParameter('modules', DI\get('modules')),
	'Starbug\Css\CssBuildCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory'))
];
?>
