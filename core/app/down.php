<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/up.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup migrations
 */
/**
 * core migration. The base migration which contains the initial database schema
 * @ingroup migrations
 */
$this->drop("emails");

$this->drop("options");

$this->drop("files");

$this->drop("uris_categories");
$this->drop("uris_tags");
$this->drop("uris_menus");
$this->drop("uris_comments");
$this->drop("blocks");
$this->drop("uris");

$this->drop("terms");

$this->drop("menus");

$this->drop("comments");

$this->drop("permits");

$this->drop("users");
?>
