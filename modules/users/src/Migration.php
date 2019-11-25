<?php
namespace Starbug\Users;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
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
