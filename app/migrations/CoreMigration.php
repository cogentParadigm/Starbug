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
			"first_name  type:string  length:64  list:true",
			"last_name  type:string  length:64  list:true",
			"username  type:string  length:128  unique:  list:true",
			"email  type:string  length:128  unique:  list:true",
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
		$this->table("permits  list:all",
			"role  type:string  length:30",
			"who  type:int  default:0",
			"action  type:string  length:100",
			"priv_type  type:string  length:30  default:table",
			"related_table  type:string  length:100",
			"related_id  type:int  default:0"
		);
		$this->table("uris",
			"path  type:string  length:64  unique:  list:true",
			"title  type:string  length:128  list:true",
			"template  type:string  length:64  default:  list:true",
			"format  type:string  length:16  default:html  list:true",
			"parent  type:int  default:0  list:false",
			"sort_order  type:int  default:0  list:false",
			"check_path  type:bool  default:1  list:false",
			"prefix  type:string  length:128  default:app/views/",
			"theme  type:string  length:128  default:  list:true",
			"layout  type:string  length:64  default:"
		);
		$this->table("blocks  list:all",
			"uris_id  type:int  references:uris id",
			"region  type:string  length:64  default:content",
			"type  type:string  length:32  default:text",
			"content  type:text  default:",
			"position  type:int  ordered:uris_id"
		);
		$this->table("menus  list:all",
			"name  type:string  length:32"
		);
		$this->table("tags  list:all",
			"tag  type:string  length:30  default:",
			"raw_tag  type:string  length:50  default:"
		);
		$this->table("uris_tags  list:all",
			"tag_id  type:int  default:0  key:primary  references:tags id  update:cascade  delete:cascade",
			"owner  type:int  default:1  key:primary  references:users id  update:cascade  delete:cascade",
			"object_id  type:int  default:0  key:primary  references:uris id  update:cascade  delete:cascade"
		);
		$this->table("files  list:all",
			"mime_type  type:string  length:128",
			"filename  type:string  length:128",
			"caption  type:string  length:255"
		);
		$this->table("options  list:all",
			"name  type:string  length:64",
			"value  type:text  default:",
			"autoload  type:bool  default:0"
		);
		$this->table("emails",
			"name  type:string  length:64  list:true",
			"subject  type:string  length:128  list:true",
			"body  type:text"
		);
		// URIS
		$this->uri("sb-admin", "format:xhr  title:Bridge  prefix:core/app/views/  groups:root");
		$this->uri("sb", "prefix:core/app/views/  groups:root"); //parent:sb-admin
		
		$this->uri("api", "template:api  prefix:core/app/views/  check_path:0");
		$this->uri("documentation", "template:documentation  prefix:core/app/views/  check_path:0  groups:root");
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
		$this->uri("rogue", "title:Rogue IDE  format:xhr  prefix:core/app/views/  groups:root");
		//Admin
		$this->uri("admin", "collective:4  theme:storm");
		//Uploader - default permission only allows root to upload
		$this->uri("upload", "prefix:core/app/views/  format:xhr  groups:root");
		
		$this->uri("list", "prefix:core/app/views/  layout:one-column  groups:user");
		$this->uri("create", "prefix:core/app/views/  layout:one-column  groups:user");
		$this->uri("update", "prefix:core/app/views/  layout:one-column  groups:user");

		// URI PERMITS
		$this->permit("uris::read", "collective:global");
		// USER PERMITS
		$this->permit("users::login", "everyone:table");
		$this->permit("users::logout", "everyone:table");
		$this->permit("users::create", "admin:");
		$this->permit("users::register", "everyone:table");
		$this->permit("users::update_profile", "owner:global");

		//LOGGING TABLES
		//ERROR LOG
		$this->table("errors",
			"type  type:string  length:64",
			"action  type:string  length:64  default:",
			"field  type:string  length:64",
			"message  type:text  length:512"
		);
		//SQL TRANSACTION LOG (MUST BE ENABLED IN etc/Etc.php)
		if (Etc::ENABLE_SQL_LOG) {
			$this->table("log",
				"table_name  type:string  length:100",
				"object_id  type:int  default:0",
				"action  type:string  length:16",
				"column_name  type:string  length:128",
				"old_value  type:text",
				"new_value  type:text"
			);
			foreach (array("users", "permits", "uris", "tags", "uris_tags", "leafs", "text_leaf", "files", "options", "emails", "errors") as $tbl) {
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
