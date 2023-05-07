<?php
namespace Starbug\Settings;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(
      ["settings_categories", "label_select" => "settings_categories.name"],
      ["name", "type" => "string", "unique" => ""],
      ["slug", "type" => "string", "slug" => "name"],
      ["position", "type" => "int", "ordered" => ""]
    );
    $this->schema->addTable("settings",
      ["name", "type" => "string", "length" => "255", "unique" => ""],
      ["type", "type" => "string", "length" => "128"],
      ["label", "type" => "string", "length" => "128"],
      ["options", "type" => "text", "default" => ""],
      ["value", "type" => "text", "default" => ""],
      ["description", "type" => "text", "default" => ""],
      ["category", "type" => "int", "references" => "settings_categories id"],
      ["autoload", "type" => "bool", "default" => "0"]
    );

    // categories
    $general = $this->schema->addRow("settings_categories", ["name" => "General"]);
    $seo = $this->schema->addRow("settings_categories", ["name" => "SEO"]);
    $email = $this->schema->addRow("settings_categories", ["name" => "Email"]);

    // general settings
    $this->schema->addRow(
      "settings",
      ["name" => "site_name"],
      ["category" => $general, "type" => "text", "label" => "Site Name", "value" => "Starbug"]
    );
    // seo settings
    $this->schema->addRow(
      "settings",
      ["name" => "meta"],
      ["category" => $seo, "type" => "textarea", "label" => "Custom Analytics, etc.."]
    );
    $this->schema->addRow(
      "settings",
      ["name" => "seo_hide"],
      ["category" => $seo, "type" => "checkbox", "value" => "1", "label" => "Hide from search engines"]
    );
    // email settings
    $this->schema->addRow(
      "settings",
      ["name" => "email_address"],
      ["category" => $email, "type" => "text", "label" => "Email Address"]
    );
    $this->schema->addRow(
      "settings",
      ["name" => "email_host"],
      ["category" => $email, "type" => "text", "label" => "Email Host"]
    );
    $this->schema->addRow(
      "settings",
      ["name" => "email_port"],
      ["category" => $email, "type" => "text", "label" => "Email Port"]
    );
    $this->schema->addRow(
      "settings",
      ["name" => "email_username"],
      ["category" => $email, "type" => "text", "label" => "Email Username"]
    );
    $this->schema->addRow(
      "settings",
      ["name" => "email_password"],
      ["category" => $email, "type" => "text", "label" => "Email Password"]
    );
    $this->schema->addRow(
      "settings",
      ["name" => "email_secure"],
      ["category" => $email, "type" => "select", "options" => "{\"options\":\",ssl,tls\"}", "label" => "Secure SMTP"]
    );
  }
}
