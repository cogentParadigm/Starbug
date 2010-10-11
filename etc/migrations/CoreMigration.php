<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file etc/migrations/CoreMigration.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * CoreMigration class. The base migration which contains the initial database schema
 * @ingroup db
 */
class CoreMigration extends Migration {
	function up() {
		// This adds a table to the schema, The Schemer builds up a schema with all of the migrations that are to be run, and then updates the db
		$this->table("users",
			"username  type:string  length:128",
			"email  type:string  length:128  unique:",
			"password  type:password  confirm:password_confirm  md5:  optional_update:",
			"memberships  type:int"
		);
		//This will be stored immediately after the creation of the users table
		$this->store("users", "username:root", "memberships:1  password:".md5(uniqid(rand(), true)));
		$this->table("permits",
			"role  type:string  length:30",
			"who  type:int  default:0",
			"action  type:string  length:100",
			"priv_type  type:string  length:30  default:table",
			"related_table  type:string  length:100",
			"related_id  type:int  default:0"
		);
		$this->table("uris",
			"path  type:string  length:64",
			"template  type:string  length:64",
			"title  type:string  length:128",
			"parent  type:int  default:0",
			"sort_order  type:int  default:0",
			"check_path  type:bool  default:1",
			"prefix  type:string  length:128  default:app/views/",
			"options  type:text"
		);
		$this->table("tags",
			"tag  type:string  length:30  default:",
			"raw_tag  type:string  length:50  default:"
		);
		$this->table("uris_tags",
			"tag_id  type:int  default:0  key:primary  references:tags id  update:cascade  delete:cascade",
			"owner  type:int  default:1  key:primary  references:users id  update:cascade  delete:cascade",
			"object_id  type:int  default:0  key:primary  references:uris id  update:cascade  delete:cascade"
		);
		$this->table("leafs",
			"leaf  type:string  length:128",
			"page  type:string  length:64",
			"container  type:string  length:32",
			"position  type:int"
		);
		$this->table("text_leaf",
			"page  type:string  length:64",
			"container  type:string  length:32",
			"position  type:int",
			"content  type:text  length:5000"
		);
		$this->table("files",
			"mime_type  type:string  length:128",
			"filename  type:string  length:128",
			"caption  type:string  length:255"
		);
		$this->table("options",
			"name  type:string  length:64",
			"value  type:text",
			"autoload  type:bool  default:0"
		);
		// URIS
		$this->uri("sb-admin", "template:templates/Login  title:Bridge  prefix:core/app/views/");
		$this->uri("sb", "template:templates/Starbug  prefix:core/app/views/  collective:1"); //parent:sb-admin
		$this->uri("sb/generate", "template:sb/generate  prefix:core/app/views/  collective:1"); //parent:sb-admin
		$this->uri("api", "template:Api  prefix:core/app/views/  check_path:0");
		//HOME PAGE
		$this->uri(Etc::DEFAULT_PATH, "template:".Etc::DEFAULT_TEMPLATE);
		//404 PAGE
		$this->uri("missing", "template:".Etc::DEFAULT_TEMPLATE);
		//403 PAGE
		$this->uri("forbidden", "template:".Etc::DEFAULT_TEMPLATE);
		// PERMITS
		$this->permit("users::login", "everyone:table");
		$this->permit("users::out", "everyone:table");
		$this->permit("uris::read", "collective:global");
	}

	function down() {
		$this->drop("options");
		$this->drop("files");
		$this->drop("text_leaf");
		$this->drop("leafs");
		$this->drop("uris_tags");
		$this->drop("tags");
		$this->drop("uris");
		$this->drop("permits");
		$this->drop("users");
	}
}
?>
