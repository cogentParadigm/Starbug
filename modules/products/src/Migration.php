<?php
namespace Starbug\Products;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(
      ["product_categories", "label_select" => "product_categories.name", "singular_label" => "Product Category"],
      ["name", "type" => "string", "length" => "128"],
      [
        "path",
        "type" => "path",
        "path" => "products/category/[product_categories:id]",
        "pattern" => "products/[product_categories:name]",
        "null" => true,
        "default" => "NULL"
      ],
      ["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
      [
        "parent",
        "type" => "int",
        "references" => "product_categories id",
        "null" => true,
        "default" => "NULL",
        "materialized_path" => "tree_path"
      ],
      ["position", "type" => "int", "ordered" => "parent"],
      ["tree_path", "type" => "string", "length" => "255", "default" => ""]
    );
    $this->schema->addTable(["product_options", "label_select" => "product_options.name"],
      ["name", "type" => "string", "length" => "128"],
      ["slug", "type" => "string", "length" => "128", "default" => "", "slug" => "name"],
      ["description", "type" => "string", "length" => "255", "default" => ""],
      ["type", "type" => "string"],
      ["required", "type" => "bool", "default" => "0"],
      ["parent", "type" => "int", "default" => "0", "materialized_path" => "tree_path"],
      ["position", "type" => "int", "ordered" => "parent"],
      ["tree_path", "type" => "string", "length" => "255", "default" => ""],
      ["reference_type", "type" => "string"],
      ["columns", "type" => "int", "default" => "12"]
    );
    $this->schema->addTable(["product_types", "groups" => false, "label_select" => "product_types.name"],
      ["name", "type" => "string", "length" => "128"],
      ["slug", "type" => "string", "length" => "128", "unique" => "", "default" => "", "slug" => "name"],
      ["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
      ["content", "type" => "text", "default" => ""],
      ["options", "type" => "product_options", "table" => "product_options"]
    );
    $this->schema->addTable(["product_tags", "label_select" => "product_tags.name"],
      ["name", "type" => "string", "length" => "128"]
    );
    $this->schema->addTable(["products", "label_select" => "products.name"],
      ["type", "type" => "int", "references" => "product_types id", "alias" => "%slug%", "null" => ""],
      ["sku", "type" => "string", "unique" => ""],
      ["name", "type" => "string"],
      ["options", "type" => "product_options", "exclude" => "always"],
      ["path", "type" => "path", "path" => "product/details/[products:id]", "pattern" => "product/[products:name]", "null" => true, "default" => "NULL"],
      ["payment_type", "type" => "string", "default" => "single"],
      ["price", "type" => "int", "default" => "0", "filter_var" => FILTER_SANITIZE_NUMBER_FLOAT],
      ["interval", "type" => "int"],
      ["unit", "type" => "string"],
      ["limit", "type" => "int", "default" => "0"],
      ["published", "type" => "bool", "default" => "1"],
      ["hidden", "type" => "bool", "default" => "0"],
      ["description", "type" => "text", "default" => ""],
      ["content", "type" => "text", "default" => ""],
      ["notes", "type" => "text", "default" => ""],
      ["thumbnail", "type" => "int", "references" => "files id", "null" => "", "default" => "NULL"],
      ["photos", "type" => "files", "optional" => ""],
      ["meta_keywords", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
      ["meta_description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
      ["position", "type" => "int", "default" => "0"],
      ["categories", "type" => "product_categories"],
      ["tags", "type" => "product_tags"]
    );

    $this->schema->addTable(["products_options"],
      ["options_id", "type" => "int", "references" => "product_options id", "update" => "cascade", "delete" => "cascade", "alias" => "%slug%"],
      ["value", "type" => "string", "length" => "255", "default" => ""]
    );

    $store = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Store"]);
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/products"], ["parent" => $store, "content" => "Products"]);
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/product-types"], ["parent" => $store, "content" => "Product Types"]);
  }
}
