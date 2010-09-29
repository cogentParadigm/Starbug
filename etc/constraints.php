<?php
// FILE: etc/constraints.php
/**
 *  configuration for access control
 * 
 *  @package StarbugPHP
 *  @subpackage etc
 *  @author Ali Gangji <ali@neonrain.com>
 * 	@copyright 2008-2010 Ali Gangji
 */
/**
 * list of groups and their membership value (must be a power of 2)
 * @global array $groups
 * @name $groups
 */
global $groups;
$groups = array(
	"root"			=> 1,
	"user"			=> 2
);
/**
 * list of statuses and their numeric value (must be a power of 2)
 * @global array $statuses
 * @name $statuses
 */
global $statuses;
$statuses = array(
	"deleted"     => 1,
	"pending"     => 2,
	"public"		  => 4,
	"private"			=> 8
);
?>
