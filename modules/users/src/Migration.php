<?php
namespace Starbug\Users;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(["users", "label_select" => "CONCAT(first_name, ' ', last_name)", "groups" => true],
      ["first_name", "type" => "string", "length" => "64", "list" => "true"],
      ["last_name", "type" => "string", "length" => "64", "list" => "true"],
      ["email", "type" => "string", "length" => "128", "unique" => "", "null" => true],
      ["password", "type" => "password", "confirm" => "password_confirm", "optional_update" => ""],
      ["last_visit", "type" => "datetime", "default" => "NULL", "null" => true],
      ["password_token", "type" => "string", "default" => ""],
      ["auth_method", "type" => "string", "null" => true, "default" => "NULL"]
    );
    $this->schema->addTable(["sessions"],
      ["users_id", "type" => "int", "references" => "users id", "update" => "cascade", "delete" => "cascade"],
      ["token", "type" => "string"],
      ["expires", "type" => "datetime"]
    );
    $this->schema->addTable(["login_tokens"],
      ["users_id", "type" => "int", "references" => "users id", "update" => "cascade", "delete" => "cascade"],
      ["token_hash", "type" => "string", "length" => "255"],
      ["expires", "type" => "datetime"],
      ["consumed", "type" => "boolean", "default" => "0"],
      ["ip_address", "type" => "string", "length" => "45", "null" => true, "default" => "NULL"]
    );
    $this->schema->addIndex("login_tokens", ["token_hash"]);
    $this->schema->addTable(["groups", "label_select" => "groups.name"],
      ["name", "type" => "string", "length" => "128", "unique" => ""],
      ["slug", "type" => "string", "length" => "128", "unique" => "", "slug" => "name"],
      ["position", "type" => "int", "ordered" => ""]
    );
    $this->schema->addColumn("users",
      ["groups", "type" => "groups", "user_access" => true, "alias" => "%slug%", "user_groups" => true]
    );
    $this->schema->addUniqueIndex("users_groups", ["users_id", "groups_id"]);
    $this->schema->addTable(["permits"],
      ["role", "type" => "string", "length" => "30"],
      ["who", "type" => "int", "default" => "0"],
      ["action", "type" => "string", "length" => "100"],
      ["priv_type", "type" => "string", "length" => "30", "default" => "table"],
      ["related_table", "type" => "string", "length" => "100"],
      ["related_id", "type" => "int", "default" => "0"],
      ["user_groups", "type" => "int", "references" => "groups id", "alias" => "%slug%", "null" => true, "default" => "NULL"]
    );

    // groups
    $this->schema->addRow("groups", ["name" => "User"]);
    $this->schema->addRow("groups", ["name" => "Admin"]);
    // USER PERMITS
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "login", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "logout", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "register", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "updateProfile", "role" => "self", "priv_type" => "global"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "forgotPassword", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "resetPassword", "role" => "everyone", "priv_type" => "table"]);

    // Email Templates
    $this->schema->addRow(
      "email_templates",
      ["name" => "Forgot Password"],
      [
        "subject" => "Your [site:name] password reset request",
        "body" => '<p>Follow this link to reset your password: [user:password-reset-link].<br />Click <a href="[url:login]">here</a> to return to the login page.</p>'
      ]
    );
    $this->schema->addRow(
      "email_templates",
      ["name" => "Login Link"],
      [
        "subject" => "Log in to [site:name]",
        "body" => '<p>Click the link below to sign in. This link expires in 15 minutes.<br/><br/><a href="[user:login-link]">[user:login-link]</a></p>'
      ]
    );
    $this->schema->addRow(
      "email_templates",
      ["name" => "Registration"],
      [
        "subject" => "Welcome to [site:name]!",
        "body" => "<h2>Welcome to [site:name]!</h2>\n<p>You can login using this email address ([user:email]) at <a href=\"[url:login]\">[url:login]</a></p>"
      ]
    );
    $this->schema->addRow(
      "email_templates",
      ["name" => "Account Creation"],
      [
        "subject" => "Welcome to [site:name]!",
        "body" => "<h2>Welcome to [site:name]!</h2>\n<p>An account has been created for you. You can login at <a href=\"[url:login]\">[url:login]</a>.</p><p>Here are your credentials.<br/>login: [user:email]<br/>password: [user:password]</p>"
      ]
    );
  }
}
