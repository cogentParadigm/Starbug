<?php
return [
	'db.schema.migrations' => DI\add([
		DI\get('Starbug\Core\TestsMigration')
	])
];
?>
