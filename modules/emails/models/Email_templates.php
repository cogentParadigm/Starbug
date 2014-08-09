<?php
/**
 * email_templates model
 * @ingroup models
 */
class Email_templates {

	function create($email_template) {
		$this->store($email_template);
	}
	
	function display_admin($display, &$ops) {
		$display->add("name");
	}
	
	function display_form($display, &$ops) {
		$display->layout->add("top  left:div.col-md-6  right:div.col-md-6", "bottom  body:div.col-sm-12");
		$display->add("name  pane:left");
		$display->add("subject  pane:left");
		$display->add("from  pane:left");
		$display->add("from_name  pane:left");
		$display->add("cc  pane:right  style:height:108px");
		$display->add("bcc  pane:right  style:height:108px");
		$display->add("body  pane:body");
	}

}
?>
