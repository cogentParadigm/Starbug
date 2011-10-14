<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file app/migrations/CoreMigration.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup migrations
 */
/**
 * CoreMigration class. The base migration which contains the initial database schema
 * @ingroup migrations
 */
class CoreMigration extends Migration {
	function up() {
		global $schemer;
		// This adds a table to the schema, The Schemer builds up a schema with all of the migrations that are to be run, and then updates the db
		$this->table("users",
			"first_name  type:string  length:64",
			"last_name  type:string  length:64",
			"username  type:string  length:128  unique:",
			"email  type:string  length:128  unique:",
			"password  type:password  confirm:password_confirm  md5:  optional_update:",
			"memberships  type:int",
			"address  type:string  length:128",
			"address2  type:string  length:128  default:",
			"city  type:string  length:32",
			"state  type:string  length:32",
			"country  type:string  length:64",
			"zip  type:string  length:16"
		);
		//This will be stored immediately after the creation of the users table
		$this->store("users", "username:root", "memberships:1");
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
			"theme  type:string  length:128  default:",
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
			"value  type:text  default:",
			"autoload  type:bool  default:0"
		);
		$this->table("emails",
			"name  type:string  length:64",
			"subject  type:string  length:128",
			"body  type:text"
		);
		// URIS
		$this->uri("sb-admin", "template:Login  title:Bridge  prefix:core/app/views/");
		$this->uri("sb", "template:Starbug  prefix:core/app/views/  collective:1"); //parent:sb-admin
		$this->uri("sb/generate", "template:../sb/generate  prefix:core/app/views/  collective:1"); //parent:sb-admin
		$this->uri("api", "template:../Api  prefix:core/app/views/  check_path:0");
		$this->uri("documentation", "template:Documentation  prefix:core/app/views/  collective:1  check_path:0");
		//HOME PAGE
		$this->uri(Etc::DEFAULT_PATH);
		//404 PAGE
		$this->uri("missing");
		//403 PAGE
		$this->uri("forbidden");
		//LOGIN/LOGOUT PAGES
		$this->uri("login");
		$this->uri("logout");
		$this->uri("forgot-password");
		//Rogue IDE
		$this->uri("rogue", "title:Rogue IDE  template:Blank  prefix:core/app/views/  collective:0");
		//Admin
		$this->uri("admin", "title:Admin  template:Admin  collective:4  theme:storm");
		//Uploader - default permission only allows root to upload
		$this->uri("upload", "prefix:core/app/views/  template:Blank  collective:1");

		// URI PERMITS
		$this->permit("uris::read", "collective:global");
		// USER PERMITS
		$this->permit("users::login", "everyone:table");
		$this->permit("users::logout", "everyone:table");
		$this->permit("users::create", "admin:");
		$this->permit("users::register", "everyone:table");
		$this->permit("users::update_profile", "owner:global");

		//ENABLE LOGGING
		if (Etc::ENABLE_SQL_LOG) {
			$this->table("log",
				"table_name  type:string  length:100",
				"object_id  type:int  default:0",
				"action  type:string  length:16",
				"column_name  type:string  length:128",
				"old_value  type:text",
				"new_value  type:text"
			);
			foreach (array("users", "permits", "uris", "tags", "uris_tags", "leafs", "text_leaf", "files", "options", "emails") as $tbl) {
				$this->after("$tbl::insert", $this->get_logging_trigger("$tbl", "insert"));
				$this->after("$tbl::update", $this->get_logging_trigger("$tbl", "update"));
				$this->after("$tbl::delete", $this->get_logging_trigger("$tbl", "delete"));
			}
		}
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
