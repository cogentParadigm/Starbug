<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/tests/up.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup migrations
 */
/**
 * core test migration. Contains schema definitions only used for unit testing
 * @ingroup migrations
 */

/*****************************************
 * addslashes
 ****************************************/
//run the text through the addslashes function to escape quotes
$this->table("hook_store_addslashes  groups:false",
	"value  type:string  addslashes:"
);

/*****************************************
 * alias
 ****************************************/
//allow a reference field to be set by an alias
$this->table("hook_store_alias  groups:false",
	"by_email  type:int  references:users id  alias:%email%",
	"by_name  type:int  references:users id  alias:%first_name% %last_name%"
);

/*****************************************
 * category
 ****************************************/
//category field type
//you can optionally specify taxonomy:taxonomy_name
//the default taxonomy will be [table_name]_[column_name]
$this->table("hook_store_category  groups:false",
	"value  type:category"
);

/*****************************************
 * confirm
 ****************************************/
//will require value and value_confirm fields to be equal.
//A more realistic example would be password and password_confirm
$this->table("hook_store_confirm  groups:false",
	"value  type:string  confirm:value_confirm"
);

/*****************************************
 * datetime
 ****************************************/
//datetime field type
$this->table("hook_store_datetime  groups:false",
	"value  type:datetime"
);

/*****************************************
 * default
 ****************************************/
//set a default value
//often used to make values optional
//remember to use a valid value type. eg. you must specify a number for an int type field.
$this->table("hook_store_default  groups:false",
	"value  type:string  default:test",
	"value2  type:string  default:"
);

/*****************************************
 * length
 ****************************************/
//set the field length
$this->table("hook_store_length  groups:false",
	"value  type:string  length:128"
);

/*****************************************
 * materialized_path
 ****************************************/
//a materialized path is a string representing a records ancestry
//use with a parent id reference field
$this->table("hook_store_materialized_path  groups:false",
	"value_field  type:string  length:255  default:",
	"parent  type:int  default:0  materialized_path:value_field"
);

/*****************************************
 * md5
 ****************************************/
//md5 encode the value
$this->table("hook_store_md5  groups:false",
	"value  type:string  length:32  md5:"
);

/*****************************************
 * optional_update
 ****************************************/
//prevent updating the record when the update value is empty
//only real use case is for password field -
//on the user profile form, the user can optionally update their password
$this->table("hook_store_optional_update  groups:false",
	"value  type:string  optional_update:  default:"
);

/*****************************************
 * ordered
 ****************************************/
//keep a field ordered numerically
//if there are records with values 1, 2, and 3, the next record
//inserted will be stored with a value of 4.
//if then we take the record with value=1 and change the value to 3, the existing 2 and 3 will shift down accordingly
$this->table("hook_store_ordered  groups:false",
	"value  type:int  ordered:"
);

/*****************************************
 * owner
 ****************************************/
//store the logged in users id to this field or 1 if not logged in
$this->table("hook_store_owner  groups:false",
	"value  type:int  references:users id  owner:  null:"
);

/*****************************************
 * password
 ****************************************/
//password field type
//the password field results in generating a value combining:
//1.)a per-account, cryptographically secure pseudorandom salt
//2.)an authenticator token which is an interated hash of the salt of password
//this allows for secure and stateless authentication
$this->table("hook_store_password  groups:false",
	"value  type:password"
);

/*****************************************
 * references
 ****************************************/
//reference another table (creates foreign key unless you specify constraint:false)
$this->table("hook_store_references  groups:false",
	"value  type:int  references:uris id"
);

/*****************************************
 * required
 ****************************************/
//without specifying this hook, a field will only be required if it is specified empty and has no default value.
//to require the field whether it is part of the store operation or not, you can use
//the required hook with one of these values:
//insert - force this field to be specified only on insert
//update - force this field to be specified only on update
//always - always force this field to be specified
$this->table("hook_store_required  groups:false",
	"value  type:string  required:always"
);

/*****************************************
 * slug
 ****************************************/
//maintain a URL friendly copy of a field (called a slug).
//specify the field name to store the slug.
$this->table("hook_store_slug  groups:false",
	"title_field  type:string",
	"slug_field  type:string  slug:title_field"
);

/*****************************************
 * terms
 ****************************************/
//you can store terms by id, slug or title
//you can comma separate multiple terms - published,pending
//you can use a minus sign to remove terms - published,-pending
//you can use a tilde (~) to remove other terms - published,-~
$this->table("hook_store_terms  groups:false",
	"value  type:terms  taxonomy:statuses"
);

/*****************************************
 * time
 ****************************************/
//store a timestamp on insert or update of a record
$this->table("hook_store_time  groups:false",
	"creation_stamp  type:datetime  time:insert",
	"update_stamp  type:datetime  time:update"
);

/*****************************************
 * unique
 ****************************************/
//require a field to be unique.
//you can specify a set of fields to scope the uniqueness to, eg. unique:other_field
//that would only require it to be unique amongst other records with the
//same value for the field 'other_field'
$this->table("hook_store_unique  groups:false",
	"value  type:string  unique:"
);

?>
