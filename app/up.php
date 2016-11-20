<?php
/**
 * database schema, routing and permissions are all managed through this file
 *
 * There are 3 sections below: Uris, Permits, and Tables
 * see the individual sections for details
 *
 * after updating this file, run 'sb migrate' to apply changes
 */
/*********************************************************
 * Permits
 * permits allow you to define who can call what functions by submitting an HTML form.
 *
 * add a permit
 * $this->permit("[model]::[function]", ["role", "option" => "value"]);
 *
 *
 * Examples
 *
 * //create a permit to allow anyone to submit the contact form calling Users::contact
 * $this->permit("users::contact", "everyone");
 *
 * //create permits to allow users create entries in the uris table, and to update records they created
 * $this->permit("uris::create", ["everyone", "user_groups" => "user", "priv_type" => "table"]);
 * $this->permit("uris::create", ["owner", "priv_type" => "global"]);
 *********************************************************/

//GLOBAL READ AND WRITE PERMITS FOR ADMIN
$this->permit("%::%", ["everyone", "priv_type" => "%", "user_groups" => "admin"]);

// URI PERMITS
// 'collective' is a column on every table representing the groups that own the records.
// each record can have different group owners.
// We can assign permissions based on those groups:
$this->permit("uris::read", ["groups", "priv_type" => "global", "object_statuses" => "published"]);
$this->permit("menus::read", ["groups", "priv_type" => "global", "object_statuses" => "published"]);
// above, I am assigning read permissions to the owning groups (collective = owning groups).
// For example, If we have these groups: user = 2, admin = 4, editor = 8
// To make a page accessible to admins and editors, we can set collective:12 on that uri (see uris above).

// USER PERMITS
$this->permit("users::login", ["everyone", "priv_type" => "table"]);
$this->permit("users::logout", ["everyone", "priv_type" => "table"]);
$this->permit("users::register", ["everyone", "priv_type" => "table"]);
$this->permit("users::update_profile", ["self", "priv_type" => "global"]); //the 'self' role should only be used for user actions.
$this->permit("users::reset_password", ["everyone", "priv_type" => "table"]);



/*********************************************************
 * Tables
 * define database tables here. table names will be prefixed
 * with the prefix in etc/Host.php
 * You can generate a model for a table by running 'sb generate model [table-name]'
 * the model will then be in app/models/
 *
 *
 * define a table
 * $this->table("[table_name]",
 * 	["column_name", "option1" => "value1", "option2" => "value2"],
 * 	["column_name", "option1" => "value1", "option2" => "value2"]
 * );
 * key/value pairs are separated by a double space
 *
 * all tables will automatically have the following columns:
 * id  - int(11) AUTO_INCREMENT PRIMARY KEY
 * owner - int(11) foreign key reference to users id
 * collective - int(11) indicates group ownership (see etc/groups.json)
 * status - int(11) indicates row status (see etc/statuses.json)
 * created - datetime - holds creation date of row
 * modified - datetime - holds last modified time of row
 *********************************************************/





/**************************************************************************
 * Uncomment the lines below to enable triggers to log database activity
 **************************************************************************/
/*
 	foreach ($this->tables as $tbl => $columns) {
		$this->after("$tbl::insert", $this->get_logging_trigger("$tbl", "insert"));
		$this->after("$tbl::update", $this->get_logging_trigger("$tbl", "update"));
		$this->after("$tbl::delete", $this->get_logging_trigger("$tbl", "delete"));
	}
*/


?>
