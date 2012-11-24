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
 * $this->uri("[path]", "[property1]:[value1]  [property2]:[value2]");
 * some properties:
 * title - the page title
 * groups - a list of groups that have access (see etc/groups.json for available groups)
 *
 *
 * Examples
 *
 * //create a page called register that loads the view app/views/register.php
 * $this->uri("register");
 *
 * //create a page called secret-sauce that can only be accessed by users in the user group
 * $this->uri("secret-sauce", "groups:user");
 ********************************************************/
 





/*********************************************************
 * Permits
 * permits allow you to define who can call what functions by submitting an HTML form.
 * 
 * add a permit
 * $this->permit("[model]::[function]", "[group-name]:[priv-type]  [group-name]:[priv-type]");
 *
 *
 * Examples
 *
 * //create a permit to allow anyone to submit the contact form calling Users::contact
 * $this->permit("users::contact", "everyone:");
 *
 * //create permits to allow users create entries in the uris table, and to update records they created
 * $this->permit("uris::create", "user:table  owner:global");
 *********************************************************/
 





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
 * 	"[column_name]  [option1]:[value1]  [option2]:[value2]",
 * 	"[column_name]  [option1]:[value1]  [option2]:[value2]"
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
 
 
 
 
 
?>
