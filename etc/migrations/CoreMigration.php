<?php
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
		$this->table("users",
			"username  type:string  length:128",
			"email  type:string  length:128  unique:",
			"password  type:password  confirm:password_confirm  md5:  optional_update:",
			"memberships  type:int"
		);
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
			"tag_id  type:int  default:0  key:primary  index:  references:tags id  update:cascade  delete:cascade",
			"owner  type:int  default:1  key:primary  index:  references:users id  update:cascade  delete:cascade",
			"object_id  type:int  default:0  key:primary  index:  references:uris id  update:cascade  delete:cascade"
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
	}
	function created() {
		global $sb;
		//COLLECT USER INPUT
		fwrite(STDOUT, "\nPlease choose an admin password:");
		$admin_pass = str_replace("\n", "", fgets(STDIN));
		fwrite(STDOUT, "\n\nYou may log in with these credentials -");
		fwrite(STDOUT, "\nusername: admin");
		fwrite(STDOUT, "\npassword: $admin_pass\n\n");
		//OPTIONS
		store_once("options", "name:migrations  value:".serialize(array("CoreMigration")));
		store_once("options", "name:migration  value:1");
		//ADMIN USER
		$errors = store_once("users", "username:admin  password:$admin_pass  memberships:1");
		//ADMIN URIS
		register_uri("path:sb-admin  template:templates/Login  title:Bridge  prefix:core/app/views/  collective:0");
		$admin_parent = $sb->insert_id;
		register_uri("path:sb  template:templates/Starbug  title:Core  prefix:core/app/views/  parent:$admin_parent");
		register_uri("path:sb/generate  template:sb/generate  title:Generate  prefix:core/app/views/  parent:$admin_parent");
		register_uri("path:api  template:Api  title:API  prefix:core/app/views/  collective:0  check_path:0");
		//HOME PAGE
		register_uri("path:".Etc::DEFAULT_PATH."  template:".Etc::DEFAULT_TEMPLATE."  title:Home  prefix:app/views/  collective:0");
		//404 PAGE
		register_uri("path:missing  template:".Etc::DEFAULT_TEMPLATE."  title:Missing  prefix:app/views/  collective:0");
		//403 PAGE
		register_uri("path:forbidden  template:".Etc::DEFAULT_TEMPLATE."  title:Forbidden  prefix:app/views/  collective:0");
		//PRIVILIGES
		register_permit("role:everyone  model:users  action:login");
		register_permit("role:everyone  model:users  action:logout");
		register_permit("type:global  model:uris  action:read  role:collective");
		//APPLY TAGS
		$sb->import("util/tags");
		$admin_uris = $sb->get("uris")->id_list($admin_parent, "parent");
		foreach($admin_uris as $obj_id) tags::safe_tag("tags", "uris_tags", "1", $obj_id, "admin");	}

	function down() {
		$this->drop("permits");
		$this->drop("users");
		$this->drop("uris");
		$this->drop("tags");
		$this->drop("uris_tags");
		$this->drop("leafs");
		$this->drop("text_leaf");
		$this->drop("files");
	}
}
?>
