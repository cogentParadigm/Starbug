<?php
namespace Starbug\Emails;

use Exception;
use Starbug\Core\FormDisplay;

class EmailTemplatesForm extends FormDisplay {
  public $model = "email_templates";
  public $cancel_url = "admin/emails";
  public function buildDisplay($options) {
    $this->layout->add(["top", "left" => "div.col-md-6", "right" => "div.col-md-6"]);
    $this->layout->add(["bottom", "body" => "div.col-sm-12"]);
    $this->add(["name", "pane" => "left"]);
    $this->add(["subject", "pane" => "left"]);
    $this->add(["from", "pane" => "left"]);
    $this->add(["from_name", "pane" => "left"]);
    $this->add(["cc", "input_type" => "textarea", "pane" => "right", "style" => "height:108px"]);
    $this->add(["bcc", "input_type" => "textarea", "pane" => "right", "style" => "height:108px"]);
    $this->add(["body", "input_type" => "textarea", "pane" => "body"]);
  }
}
