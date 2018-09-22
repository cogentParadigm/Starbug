<?php
namespace Starbug\Emails;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable("email_templates",
      ["name", "type" => "string", "length" => "128", "list" => true],
      ["subject", "type" => "string", "length" => "155"],
      ["from", "type" => "string", "length" => "255", "default" => ""],
      ["from_name", "type" => "string", "length" => "128", "default" => ""],
      ["cc", "type" => "text", "default" => ""],
      ["bcc", "type" => "text", "default" => ""],
      ["body", "type" => "text", "class" => "rich-text"]
    );
    $this->schema->addRow(
      "email_templates",
      ["name" => "Registration"],
      array(
        "subject" => "Welcome to [site:name]!",
        "body" => "<h2>Welcome to [site:name]!</h2>\n<p>You can login using this email address ([user:email]) at <a href=\"[url:login]\">[url:login]</a></p>"
      )
    );
    $this->schema->addRow(
      "email_templates",
      ["name" => "Account Creation"],
      array(
        "subject" => "Welcome to [site:name]!",
        "body" => "<h2>Welcome to [site:name]!</h2>\n<p>An account has been created for you. You can login at <a href=\"[url:login]\">[url:login]</a>.</p><p>Here are your credentials.<br/>login: [user:email]<br/>password: [user:password]</p>"
      )
    );
    $this->schema->addRow(
      "email_templates",
      ["name" => "Password Reset"],
      array(
        "subject" => "Your [site:name] password has been reset!",
        "body" => "<p>Your new password is <strong>[user:password]</strong>. You can login at <a href=\"[url:login]\">[url:login]</a>.</p>"
      )
    );
  }
}
