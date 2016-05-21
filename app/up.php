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
 * Uris
 * a uri is an entry in the uris table which corresponds to a page
 * setting the path to "my-new-page" will mean that the page
 * can be accessed at the /my-new-page and edited at app/views/my-new-page.php
 *
 *
 * add a uri (a new page in app/views/)
 * $this->uri("[path]", ["property1" => "value1", "property2" => "value2"]);
 * some properties:
 * title - the page title
 * groups - a list of groups that have access (see etc/groups.json for available groups)
 *
 *
 * * examples
 *
 * //create a page called register that loads the view app/views/register.php
 * $this->uri("register");
 *
 * //create a page called secret-sauce that can only be accessed by users in the user group
 * $this->uri("secret-sauce", ["groups" => "user"]);
 *
 *
 * Blocks
 * a block is a CMS region. Place content in blocks if you want it to be editable.
 *
 * add a block
 * $this->block("[path]", "[content]", ["option" => "value", "option2" => "value2"]);
 * options:
 * region - a region name (default is 'content'). If an existing block is found in the same region for the same page, it will not be stored
 * type - the block type. default is 'text'
 * position - specify to add additional blocks in the same space
 *
 ********************************************************/
//HOME PAGE
$this->uri("home", ["type" => "views", "layout" => "home"]);
$this->block("home", "<h1>Congratulations, she rides!</h1>\n<p>You&#39;ve successfully installed Starbug PHP!</p>");
//404 PAGE
$this->uri("missing", ["title" => "404 - Not Found", "type" => "views"]);
$this->block("missing", "<p><strong>Oops! The page you are looking for was not found.</strong></p>");
//403 PAGE
$this->uri("forbidden", ["type" => "views"]);
//LOGIN/LOGOUT PAGES
$this->uri("login", ["controller" => "login"]);
$this->uri("logout", ["controller" => "login", "action" => "logout"]);
$this->uri("forgot-password", ["controller" => "login", "action" => "forgot_password"]);





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
