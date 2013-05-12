<?php
	$args['from'] .= " INNER JOIN ".P("log")." AS log ON log.object_id=$first.id";
	$args['where'] = "log.table_name='$first'"
									 .((empty($args['log'])) ? "" : " && ".$args['log'])
									 .((empty($args['where'])) ? "" : " && ".$args['where']);
?>
