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
// This adds a table to the schema, The Schemer builds up a schema with all of the migrations that are to be run, and then updates the db
$this->table("users  label_select:CONCAT(first_name, ' ', last_name, ' (', email, ')')",
	"first_name  type:string  length:64  list:true",
	"last_name  type:string  length:64  list:true",
	"email  type:string  length:128  unique:  list:true",
	"password  type:password  confirm:password_confirm  optional_update:",
	"address  type:string  length:128",
	"address2  type:string  length:128  default:",
	"city  type:string  length:32",
	"state  type:string  length:32",
	"country  type:string  length:64",
	"zip  type:string  length:16",
	"last_visit  type:datetime  default:0000-00-00 00:00:00  list:true  display:false"
);
//This will be stored immediately after the creation of the users table
$this->store("users", "email:root", "groups:root");
$this->table("permits  list:all",
	"role  type:string  length:30",
	"who  type:int  default:0",
	"action  type:string  length:100",
	"priv_type  type:string  length:30  default:table",
	"related_table  type:string  length:100",
	"related_id  type:int  default:0"
);
$this->table("terms  label_select:terms.term",
	"term  type:string  length:128",
	"slug  type:string  length:128  unique:taxonomy parent  display:false  default:  slug:term",
	"description  type:string  length:255  input_type:textarea  default:",
	"taxonomy  type:string  views:taxonomies  input_type:hidden",
	"parent  type:int  default:0  input_type:category_select  readonly:  materialized_path:term_path",
	"position  type:int  ordered:taxonomy parent  display:false",
	"term_path  type:string  length:255  default:  display:false"
);
$this->table("settings",
	"name  type:string  length:255",
	"type  type:string  length:128",
	"label  type:string  length:128",
	"options  type:text  default:",
	"value  type:text  default:",
	"description  type:text  default:",
	"category  type:category  null:",
	"autoload  type:bool  default:0"
);
$this->table("uris  label:Pages  singular_label:Page  label_select:title",
	"title  type:string  length:128  list:true",
	"path  type:string  length:64  unique:  list:true  slug:title  null:  pattern:[path:token]",
	"template  type:string  length:64  default:  list:false",
	"categories  type:terms  optional:",
	"tags  type:terms  column:term",
	"format  type:string  length:16  default:  list:false",
	"parent  type:int  default:0  list:false",
	"sort_order  type:int  default:0  list:false",
	"type  type:string  default:  list:false",
	"prefix  type:string  length:128  default:app/views/",
	"theme  type:string  length:128  default:  list:false",
	"layout  type:string  length:64  default:",
	"description  type:string  length:255  input_type:textarea  default:  list:false",
	"meta  type:text  default:  list:false",
	"meta_keywords  type:string  length:255  input_type:textarea  default:  list:false",
	"canonical  type:string  length:255  default:  list:false",
	"breadcrumb  type:string  length:255  default:  list:false"
);
$this->table("entities  groups:false",
	"base  type:string  default:",
	"name  type:string  length:128",
	"label  type:string  length:128",
	"singular  type:string  length:128",
	"singular_label  type:string  length:128",
	"url_pattern  type:string",
	"description  type:string  length:255  default:"
);
$this->table("blocks  list:all",
	"uris_id  type:int  references:uris id  alias:%path%",
	"region  type:string  length:64  default:content",
	"type  type:string  length:32  default:text",
	"content  type:text  default:",
	"position  type:int  ordered:uris_id"
);
$this->table("uris", "blocks  type:blocks  table:blocks");
$this->table("menus",
	"menu  type:string  length:32  list:true  display:false",
	"parent  type:int  default:0  materialized_path:menu_path",
	"uris_id  type:int  references:uris id  label:Page  null:  update:cascade  delete:cascade",
	"href  type:string  length:255  label:URL  default:",
	"content  type:string  length:255  default:",
	"target  type:string  default:",
	"template  type:string  length:128  default:",
	"position  type:int  ordered:menu parent  default:0",
	"menu_path  type:string  length:255  default:  display:false"
);
// CONTENT TYPES
$this->table("views  base:uris  description:A basic view");
$this->table("pages  base:uris  description:A basic page");
// URIS
$this->uri("api", "template:api  prefix:core/app/views/");
$this->uri("profile", "template:controller");
//Admin
$this->uri("admin", "template:controller-group  groups:admin  theme:storm");
//Uploader
$this->uri("upload", "prefix:core/app/views/  format:xhr  groups:user");
//terms
$this->uri("terms", "prefix:core/app/views/  format:xhr  groups:user");
$this->uri("robots", "prefix:core/app/views/  format:txt");

//Admin Menu
$this->menu("admin",
	array(
		"content" => "Content",
		"children" => array(
			"href:admin/views  content:Views",
			"href:admin/pages  content:Pages"
		)
	),
	"href:admin/users  content:Users",
	"href:admin/media  content:Media  target:_blank",
	array(
		"content" => "Configuration",
		"children" => array(
			"href:admin/taxonomies  content:Taxonomy",
			"href:admin/menus  content:Menus",
			"href:admin/emails  content:Email Templates",
			"href:admin/settings  content:Settings"
		)
	)
);
//groups
$this->taxonomy("groups",
	"term:Root",
	"term:User",
	"term:Admin"
);
//statuses
$this->taxonomy("statuses",
	"term:Deleted",
	"term:Pending",
	"term:Published",
	"term:Private"
);
//uris categories
$this->taxonomy("uris_categories",
	"term:Uncategorized"
);
//uris tags
$this->taxonomy("uris_tags",
	"term:Uncategorized"
);
//settings categories
$this->taxonomy("settings_category",
	"term:General",
	"term:SEO",
	"term:Themes",
	"term:Email"
);

//general settings
$this->store("settings", "name:site_name", "category:settings_category general  type:text  label:Site Name  autoload:1  value:Starbug");
$this->store("settings", "name:tagline", "category:settings_category general  type:text  label:Tagline  autoload:1  value:Fresh XHTML and CSS, just like mom used to serve!");
$this->store("settings", "name:default_path", "category:settings_category general  type:text  label:Default Path  autoload:1  value:home");
//seo settings
$this->store("settings", "name:meta",  "category:settings_category seo  type:textarea  label:Custom Analytics, etc..  autoload:1");
$this->store("settings", "name:seo_hide",  "category:settings_category seo  type:checkbox  value:1  label:Hide from search engines  autoload:1");
//theme settings
$this->store("settings", "name:theme",  "category:settings_category themes  type:text  label:Theme  autoload:1  value:starbug-1");
//email settings
$this->store("settings", "name:email_address", "category:settings_category email  type:text  label:Email Address");
$this->store("settings", "name:email_host", "category:settings_category email  type:text  label:Email Host");
$this->store("settings", "name:email_port", "category:settings_category email  type:text  label:Email Port");
$this->store("settings", "name:email_username", "category:settings_category email  type:text  label:Email Username");
$this->store("settings", "name:email_password", "category:settings_category email  type:text  label:Email Password");
$this->store("settings", "name:email_secure", "category:settings_category email  type:select  options:{\"options\":\",ssl,tls\"}  label:Secure SMTP");

//LOGGING TABLES
//ERROR LOG
$this->table("errors",
	"type  type:string  length:64",
	"action  type:string  length:64  default:",
	"field  type:string  length:64",
	"message  type:text  length:512"
);
//SQL TRANSACTION LOG
	$this->table("log",
		"table_name  type:string  length:100",
		"object_id  type:int  default:0",
		"action  type:string  length:16",
		"column_name  type:string  length:128",
		"old_value  type:text",
		"new_value  type:text"
	);
?>
