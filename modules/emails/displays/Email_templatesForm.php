<?php
namespace Starbug\Emails;
use Starbug\Core\FormDisplay;
class Email_templatesForm extends FormDisplay {
	public $model = "email_templates";
	public $cancel_url = "admin/emails";
	function build_display($options) {
		$this->layout->add("top  left:div.col-md-6  right:div.col-md-6", "bottom  body:div.col-sm-12");
		$this->add("name  pane:left");
		$this->add("subject  pane:left");
		$this->add("from  pane:left");
		$this->add("from_name  pane:left");
		$this->add("cc  pane:right  style:height:108px");
		$this->add("bcc  pane:right  style:height:108px");
		$this->add("body  pane:body");
	}
}
?>
