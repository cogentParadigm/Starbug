<?php
namespace Starbug\Payment;
use Starbug\Core\FormDisplay;
class ProductsForm extends FormDisplay {
	public $model = "products";
	public $cancel_url = "admin/products";
	function build_display($options) {
		$this->layout->add(["top", "left" => "div.col-md-9", "right" => "div.col-md-3"]);
		$this->layout->add(["bottom", "tabs" => "div.col-sm-12"]);
		$this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($ops['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Meta tags"]'.(($ops['tab'] === "meta") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'meta');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Publishing options"]'.(($ops['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'publishing');
		$this->add(["type", "input_type" => "select", "from" => "product_types", "pane" => "left"]);
		$this->add(["sku", "label" => "SKU", "pane" => "left"]);
		$this->add(["name", "pane" => "left"]);
		$this->add(["price", "pane" => "left", "info" => "Enter price in cents. For example $50 should be entered as 5000"]);
		$this->add(["description", "pane" => "left"]);
		$this->add(["content", "pane" => "left"]);
		$this->add(["thumbnail", "input_type" => "file_select", "pane" => "left"]);
		$this->add(["photos", "input_type" => "file_select", "pane" => "left"]);
		$this->add(["position", "pane" => "right"]);
		$this->add(["categories", "pane" => "right"]);
		$this->add(["path", "label" => "URL path", "info" => "Leave empty to generate automatically", "pane" => "path"]);
		$this->add(["meta_description", "label" => "Meta Description", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
		$this->add(["meta_keywords", "label" => "Meta Keywords", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
		$this->add(["published", "pane" => "publishing", "value" => "1"]);
		$this->add(["hidden", "pane" => "publishing", "value" => "1"]);
	}
}
?>
