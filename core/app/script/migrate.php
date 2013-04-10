<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/migrate.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$next = array_shift($argv);
if (false !== strpos($next, ":")) {
	$next = explode(":", $next);
	$from = $next[0];
	$to = $next[1];
	$schemer->migrate($to, $from);
} else if ((!empty($next)) && (0 !== $next)) {
	$to = $next;
	$schemer->migrate($to);
} else {
	$schemer->migrate();
}
?>