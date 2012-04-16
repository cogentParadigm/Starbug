<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file modules/emails/up.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup migrations
 */
/**
 * emails module migration
 * @ingroup migrations
 */
$this->table("emails",
	"name  type:string  length:64  list:true",
	"subject  type:string  length:128  list:true",
	"body  type:text"
);
$this->permit("emails::create", "admin:");
$this->permit("emails::delete", "admin:global");
$this->permit("emails::read", "admin:global");
?>
