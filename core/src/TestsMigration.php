<?php
namespace Starbug\Core;

use Starbug\Db\Schema\AbstractMigration;

class TestsMigration extends AbstractMigration {
  public function up() {
    /*****************************************
     * Table for testing addslashes
     ****************************************/
    // run the text through the addslashes function to escape quotes
    $this->schema->addTable(["hook_store_addslashes"],
      ["value", "type" => "string", "addslashes" => ""]
    );

    /*****************************************
     * Table for testing alias
     ****************************************/
    // allow a reference field to be set by an alias
    $this->schema->addTable(["hook_store_alias"],
      ["by_email", "type" => "int", "references" => "users id", "alias" => "%email%"],
      ["by_name", "type" => "int", "references" => "users id", "alias" => "%first_name% %last_name%"]
    );

    /*****************************************
     * Table for testing confirm
     ****************************************/
    // will require value and value_confirm fields to be equal.
    // A more realistic example would be password and password_confirm
    $this->schema->addTable(["hook_store_confirm"],
      ["value", "type" => "string", "confirm" => "value_confirm"]
    );

    /*****************************************
     * Table for testing datetime
     ****************************************/
    // datetime field type
    $this->schema->addTable(["hook_store_datetime"],
      ["value", "type" => "datetime"]
    );

    /*****************************************
     * Table for testing default
     ****************************************/
    // set a default value
    // often used to make values optional
    // remember to use a valid value type. eg. you must specify a number for an int type field.
    $this->schema->addTable(["hook_store_default"],
      ["value", "type" => "string", "default" => "test"],
      ["value2", "type" => "string", "default" => ""]
    );

    /*****************************************
     * Table for testing length
     ****************************************/
    // set the field length
    $this->schema->addTable(["hook_store_length"],
      ["value", "type" => "string", "length" => "128"]
    );

    /*****************************************
     * Table for testing materialized_path
     ****************************************/
    // a materialized path is a string representing a records ancestry
    // use with a parent id reference field
    $this->schema->addTable(["hook_store_materialized_path"],
      ["value_field", "type" => "string", "length" => "255", "default" => ""],
      ["parent", "type" => "int", "default" => "0", "materialized_path" => "value_field"]
    );

    /*****************************************
     * Table for testing md5
     ****************************************/
    // md5 encode the value
    $this->schema->addTable(["hook_store_md5"],
      ["value", "type" => "string", "length" => "32", "md5" => ""]
    );

    /*****************************************
     * Table for testing optional_update
     ****************************************/
    // prevent updating the record when the update value is empty
    // only real use case is for password field -
    // on the user profile form, the user can optionally update their password
    $this->schema->addTable(["hook_store_optional_update"],
      ["value", "type" => "string", "optional_update" => "", "default" => ""]
    );

    /*****************************************
     * Table for testing ordered
     ****************************************/
    // keep a field ordered numerically
    // if there are records with values 1, 2, and 3, the next record
    // inserted will be stored with a value of 4.
    // if then we take the record with value=1 and change the value to 3, the existing 2 and 3 will shift down accordingly
    $this->schema->addTable(["hook_store_ordered"],
      ["value", "type" => "int", "ordered" => ""]
    );

    /*****************************************
     * Table for testing owner
     ****************************************/
    // store the logged in users id to this field or 1 if not logged in
    $this->schema->addTable(["hook_store_owner"],
      ["value", "type" => "int", "references" => "users id", "owner" => "", "null" => ""]
    );

    /*****************************************
     * Table for testing password
     ****************************************/
    // password field type
    // the password field results in generating a value combining:
    // 1.)a per-account, cryptographically secure pseudorandom salt
    // 2.)an authenticator token which is an interated hash of the salt of password
    // this allows for secure and stateless authentication
    $this->schema->addTable(["hook_store_password"],
      ["value", "type" => "password"]
    );

    /*****************************************
     * Table for testing references
     ****************************************/
    // reference another table (creates foreign key unless you specify constraint:false)
    $this->schema->addTable(["hook_store_references"],
      ["value", "type" => "int", "references" => "users id"]
    );

    /*****************************************
     * Table for testing required
     ****************************************/
    // without specifying this hook, a field will only be required if it is specified empty and has no default value.
    // to require the field whether it is part of the store operation or not, you can use
    // the required hook with one of these values:
    // insert - force this field to be specified only on insert
    // update - force this field to be specified only on update
    // always - always force this field to be specified
    $this->schema->addTable(["hook_store_required"],
      ["value", "type" => "string", "required" => "always"]
    );

    /*****************************************
     * Table for testing slug
     ****************************************/
    // maintain a URL friendly copy of a field (called a slug).
    // specify the field name to store the slug.
    $this->schema->addTable(["hook_store_slug"],
      ["title_field", "type" => "string"],
      ["slug_field", "type" => "string", "slug" => "title_field"]
    );

    /*****************************************
     * Table for testing time
     ****************************************/
    // store a timestamp on insert or update of a record
    $this->schema->addTable(["hook_store_time"],
      ["creation_stamp", "type" => "datetime", "timestamp" => "insert"],
      ["update_stamp", "type" => "datetime", "timestamp" => "update"]
    );

    /*****************************************
     * Table for testing unique
     ****************************************/
    // require a field to be unique.
    // you can specify a set of fields to scope the uniqueness to, eg. unique:other_field
    // that would only require it to be unique amongst other records with the
    // same value for the field 'other_field'
    $this->schema->addTable(["hook_store_unique"],
      ["value", "type" => "string", "unique" => ""]
    );
  }
}
