<?php
return [
	'db.schema.migrations' => DI\add([
		DI\get('Starbug\Emails\Migration')
	])
];
