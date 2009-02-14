#!/usr/bin/php
<?php
		include("Etc.php");
		include("init.php");
		$base = dirname(__FILE__)."/../core/db/";
		$uris = unserialize(file_get_contents($base."schema/uris"));
		$users = unserialize(file_get_contents($base."schema/users"));
		include($base."Schemer.php");
		$schemer = new Schemer($db);
		$schemer->create("uris", $uris);
		$schemer->create("users", $users);
		$schemer->insert("users", "first_name, last_name, email, password, security", "'$argv[1]', '$argv[2]', '$argv[3]', '".md5($argv[4])."', '".Etc::SUPER_ADMIN_SECURITY."'");
		$schemer->insert("uris", "path, template, security", "'uris', 'Starbug', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/new', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/get', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/edit', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/add', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, security", "'models', 'Starbug', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/new', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/get', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/edit', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/add', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/remove', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, security", "'users', 'Starbug', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/new', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/get', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/edit', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/add', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'login', 'Starbug', '0', '0'");
?>