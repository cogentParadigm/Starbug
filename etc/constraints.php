<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file etc/constraints.php configuration for access control
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup etc
 */
/**
 * list of groups and their membership value (must be a power of 2)
 * @ingroup etc
 */
global $groups;
$groups = array(
	"root"			=> 1,
	"user"			=> 2
);
/**
 * list of statuses and their numeric value (must be a power of 2)
 * @ingroup etc
 */
global $statuses;
$statuses = array(
	"deleted"     => 1,
	"pending"     => 2,
	"public"		  => 4,
	"private"			=> 8
);
?>
