<?php
namespace Starbug\Payment;
use Starbug\Db\Schema\AbstractMigration;
class Migration extends AbstractMigration {
	public function up() {
		$this->schema->addTable(["payment_gateways", "label_select" => "payment_gateways.name", "groups" => false],
			["name", "type" => "string", "length" => "255"],
			["description", "type" => "text", "default" => ""],
			["is_active", "type" => "bool", "default" => "0"],
			["is_test_mode", "type" => "bool", "default" => "0"]
		);
		$this->schema->addTable(["payment_gateway_settings", "label_select" => "payment_gateway_settings.name", "groups" => false],
			["payment_gateway_id", "type" => "int", "references" => "payment_gateways id", "alias" => "%name%"],
			["name", "type" => "string", "length" => "256"],
			["type", "type" => "string", "input_type" => "select", "options" => "text,textarea,select,checkbox,radio,password"],
			["options", "type" => "text", "default" => ""],
			["test_mode_value", "type" => "text", "default" => ""],
			["live_mode_value", "type" => "text", "default" => ""],
			["description", "type" => "text", "default" => ""]
		);
		//store payment gateways
		$this->schema->addRow("payment_gateways",
			["name" => "Authorize.Net", "description" => "Purchase with credit card using Authorize.net"],
			["is_test_mode" => "1", "is_active" => "1"]
		);

		$this->schema->addTable(["product_types", "groups" => false, "label_select" => "product_types.name"],
			["name", "type" => "string", "length" => "128"],
			["slug", "type" => "string", "length" => "128", "unique" => "", "default" => "", "slug" => "name"],
			["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
			["content", "type" => "text", "default" => ""]
		);
		$this->schema->addTable(["products", "groups" => false],
			["type", "type" => "int", "references" => "product_types id", "alias" => "%slug%", "null" => ""],
			["sku", "type" => "string", "unique" => ""],
			["name", "type" => "string"],
			["path", "type" => "string", "length" => "128", "unique" => "", "default" => "", "slug" => "name"],
			["payment_type", "type" => "string", "default" => "single"],
			["price", "type" => "int", "default" => "0"],
			["interval", "type" => "int"],
			["unit", "type" => "string"],
			["limit", "type" => "int", "default" => 0],
			["published", "type" => "bool", "default" => "1"],
			["hidden", "type" => "bool", "default" => "0"],
			["description", "type" => "text", "default" => ""],
			["content", "type" => "text", "default" => ""],
			["notes", "type" => "text", "default" => ""],
			["thumbnail", "type" => "int", "references" => "files id", "null" => "", "default" => "NULL"],
			["photos", "type" => "files", "optional" => ""],
			["position", "type" => "int", "ordered" => "type"],
			["categories", "type" => "terms", "optional" => ""],
			["meta_keywords", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
			["meta_description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
			["sorting_weight", "type" => "int", "default" => "0"]
		);
		$this->schema->addTable(["lines", "groups" => false],
			["type", "type" => "string"],
			["description", "type" => "string", "length" => "255"],
			["price", "type" => "int", "default" => "0"],
			["qty", "type" => "int", "default" => "1"]
		);
		$this->schema->addTable(["product_lines", "base" => "lines", "groups" => false],
			["product", "type" => "int", "references" => "products id"]
		);
		$this->schema->addTable(["shipping_lines", "base" => "lines", "groups" => false]);
		$this->schema->addTable(["tax_lines", "base" => "lines", "groups" => false]);
		$this->schema->addTable(["payment_cards", "groups" => false],
			["customer_reference", "type" => "string", "length" => "128", "default" => ""],
			["card_reference", "type" => "string", "length" => "128"],
			["brand", "type" => "string"],
			["number", "type" => "string"],
			["month", "type" => "int"],
			["year", "type" => "int"]
		);
		$this->schema->addTable(["payments", "groups" => false],
			["amount", "type" => "int", "default" => "0"],
			["response_code", "type" => "int"],
			["txn_id", "type" => "string", "length" => "32", "default" => ""],
			["card", "type" => "int", "references" => "payment_cards id", "null" => true, "default" => "NULL"],
			["response", "type" => "text"]
		);
		$this->schema->addTable(["subscriptions", "groups" => false],
			["product", "type" => "int", "references" => "products id"],
			["amount", "type" => "int", "default" => "0"],
			["start_date", "type" => "datetime"],
			["interval", "type" => "int"],
			["unit", "type" => "string"],
			["limit", "type" => "int", "default" => 0],
			["card", "type" => "int", "references" => "payment_cards id"],
			["expiration_date", "type" => "datetime"],
			["payments", "type" => "payments", "table" => "payments"],
			["active", "type" => "bool", "default" => 1],
			["canceled", "type" => "bool", "default" => 0],
			["completed", "type" => "bool", "default" => 0]
		);
		$this->schema->addTable(["bills", "groups" => false],
			["amount", "type" => "int", "default" => "0"],
			["due_date", "type" => "datetime"],
			["scheduled_date", "type" => "datetime"],
			["subscriptions_id", "type" => "int", "references" => "subscriptions id", "null" => true, "default" => "NULL"],
			["payments", "type" => "payments", "table" => "payments"],
			["scheduled", "type" => "bool", "default" => "0"],
			["paid", "type" => "bool", "default" => "0"]
		);
		$this->schema->addTable(["orders", "search" => "orders.id,orders.order_status,orders.email,orders.phone,orders.billing_address.recipient,orders.shipping_address.recipient", "groups" => false],
			["subtotal", "type" => "string", "length" => "32", "default" => ""],
			["total", "type" => "string", "length" => "32"],
			["order_status", "type" => "string", "length" => "128", "default" => "cart"],
			["lines", "type" => "lines", "table" => "lines", "optional" => ""],
			["token", "type" => "string", "length" => "128", "default" => ""],
			["billing_address", "type" => "int", "references" => "address id", "null" => "", "operation" => "create"],
			["shipping_address", "type" => "int", "references" => "address id", "null" => "", "operation" => "create"],
			["email", "type" => "string", "length" => "128"],
			["phone", "type" => "string"],
			["payments", "type" => "payments", "table" => "payments"],
			["subscriptions", "type" => "subscriptions", "table" => "subscriptions"],
			["bills", "type" => "bills", "table" => "bills"]
		);

		$this->schema->addRow("permits", ["related_table" => "users", "action" => "login", "role" => "everyone", "priv_type" => "table"]);
		$this->schema->addRow("permits", ["related_table" => "orders", "action" => "checkout", "role" => "owner", "priv_type" => "global"]);
		$this->schema->addRow("permits", ["related_table" => "orders", "action" => "payment", "role" => "owner", "priv_type" => "global"]);
		$this->schema->addRow("permits", ["related_table" => "subscriptions", "action" => "update", "role" => "owner", "priv_type" => "global"]);
		$this->schema->addRow("permits", ["related_table" => "subscriptions", "action" => "cancel", "role" => "owner", "priv_type" => "global"]);
		$this->schema->addRow("permits", ["related_table" => "subscriptions", "action" => "payment", "role" => "owner", "priv_type" => "global"]);

		$this->schema->addRow(
			"email_templates",
			["name" => "Order Confirmation"],
			array(
				"subject" => "Your [site:name] order confirmation",
				"body" => "<h2>Order confirmation</h2>\n".
					"<h3>Hello [user:first_name],</h3>\n".
					"<p>Thank you for your purchase. You ordered [order:description].</p>\n".
					"<h3>Details</h3>\n".
					"[order:details]"
			)
		);
		$this->schema->addRow(
			"email_templates",
			["name" => "Update Subscription"],
			array(
				"subject" => "Your [site:name] subscription",
				"body" => "<h2>Your subscription</h2>\n".
					"<h3>Hello [user:first_name],</h3>\n".
					"<p>Your [subscription:description] subscription has been updated.</p>\n".
					"<h3>Details</h3>\n".
					"[subscription:details]"
			)
		);
		$this->schema->addRow(
			"email_templates",
			["name" => "Payment Confirmation"],
			array(
				"subject" => "Your [site:name] payment confirmation",
				"body" => "<h2>Payment confirmation</h2>\n".
					"<h3>Hello [user:first_name],</h3>\n".
					"<p>Thank you for your payment.</p>\n".
					"<h3>Details</h3>\n".
					"[payment:details]"
			)
		);
		$this->schema->addRow(
			"email_templates",
			["name" => "Payment Declined"],
			array(
				"subject" => "Your [site:name] payment was declined",
				"body" => "<h2>Payment declined</h2>\n".
					"<h3>Hello [user:first_name],</h3>\n".
					"<p>Your payment was unsuccessful.</p>\n".
					"<h3>Details</h3>\n".
					"[payment:details]"
			)
		);
	}
}
