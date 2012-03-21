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
		$this->table("options  list:all",
			"name  type:string  length:64",
			"value  type:text  default:",
			"autoload  type:bool  default:0"
		);
		$this->store("options", "name:meta", "value:");
		$this->store("options", "name:seo_hide", "value:1");
		$this->table("terms",
			"term  type:string  length:128",
			"slug  type:string  length:128  unique:taxonomy parent  display:false",
			"description  type:string  length:255  input_type:textarea  default:",
			//"attachment  type:int  upload:term_attachment  null:  references:files id",
			"taxonomy  type:string  views:taxonomies  input_type:hidden",
			"parent  type:int  default:0  input_type:category_select  readonly:",
			"position  type:int  ordered:taxonomy parent  display:false"
		);
		$this->table("files  list:all",
			"mime_type  type:string  length:128",
			"filename  type:string  length:128",
			"category  type:category  null:",
			"caption  type:string  length:255",
			"directory  type:int  default:1  display:false"
		);
		$this->table("comments",
			"name  type:string  length:255",
			"email  type:string  length:255",
			"comment  type:text"
		);
		$this->table("uris  label:Pages  singular_label:Page",
			"path  type:string  length:64  unique:  list:true",
			"title  type:string  length:128  list:true",
			"template  type:string  length:64  default:  list:false",
			"categories  type:terms",
			"tags  type:terms",
			"format  type:string  length:16  default:html  list:false",
			"parent  type:int  default:0  list:false",
			"sort_order  type:int  default:0  list:false",
			"type  type:string  default:View  list:false",
			"prefix  type:string  length:128  default:app/views/",
			"theme  type:string  length:128  default:  list:false",
			"layout  type:string  length:64  default:",
			"description  type:string  length:255  input_type:textarea  default:  list:false",
			"meta  type:text  default:  list:false",
			"canonical  type:string  length:255  default:  list:false",
			"breadcrumb  type:string  length:255  default:  list:false",
			"comments  type:comments  display:false"
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
		$this->table("uris_menus  list:all",
			"uris_id  type:int  references:uris id  update:cascade  delete:cascade",
			"menus_id  type:int  references:menus id  update:cascade  delete:cascade",
			"position  type:int  ordered:menus_id parent",
			"parent  type:int  default:0"
		);
		$this->table("emails",
			"name  type:string  length:64  list:true",
			"subject  type:string  length:128  list:true",
			"body  type:text"
		);
		// URIS
		$this->uri("sb-admin", "format:xhr  title:Bridge  prefix:core/app/views/  groups:root");
		$this->uri("sb", "prefix:core/app/views/  groups:root"); //parent:sb-admin
		
		$this->uri("api", "template:api  prefix:core/app/views/  type:Page");
		$this->uri("documentation", "template:documentation  prefix:core/app/views/  type:Page  groups:root");
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
		$this->uri("terms", "prefix:core/app/views/  format:xhr  groups:user");
		$this->uri("robots", "prefix:core/app/views/  format:txt");

		// PERMITS
		//STANDARD WRITE PERMITS
		foreach(array("users", "terms", "menus", "uris_menus", "uris", "options") as $standard_write) {
						$this->permit("$standard_write::create", "admin:");
						$this->permit("$standard_write::delete", "admin:global");
		}
		//STANDARD READ PERMITS
		foreach (array("terms", "menus", "uris_menus") as $standard_read) {
						$this->permit("$standard_read::read","user:global");
		}
		// URI PERMITS
		$this->permit("uris::read", "collective:global 4");
		$this->permit("uris::update", "admin:global");
		$this->permit("uris::apply_tags", "admin:global");
		$this->permit("uris::remove_tag", "admin:global");
		// USER PERMITS
		$this->permit("users::login", "everyone:table");
		$this->permit("users::logout", "everyone:table");
		$this->permit("users::create", "admin:");
		$this->permit("users::register", "everyone:table");
		$this->permit("users::update_profile", "owner:global");
		$this->permit("users::reset_password", "everyone:table");
		// MENU PERMITS
		$this->permit("menus::add_uri", "admin:global");
		// TERM PERMITS
		$this->permit("terms::delete_taxonomy", "admin:table");
		//FILE PERMITS
		$this->permit("files::prepare", "admin:table");

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
		$this->drop("emails");
		$this->drop("options");
		$this->drop("files");
		$this->drop("uris_tags");
		$this->drop("tags");
		$this->drop("uris_menus");
		$this->drop("menus");
		$this->drop("blocks");
		$this->drop("uris");
		$this->drop("permits");
		$this->drop("users");
	}
}
?>
