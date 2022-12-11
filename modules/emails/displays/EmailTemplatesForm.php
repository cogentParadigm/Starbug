<?php
namespace Starbug\Emails;

use Exception;
use Starbug\Core\FormDisplay;

class EmailTemplatesForm extends FormDisplay {
  public $model = "email_templates";
  public function buildDisplay($options) {
    $this->layout->add(["top", "left" => "div.col-6", "right" => "div.col-6"]);
    $this->layout->add(["bottom", "body" => "div.col-12"]);
    $this->add(["name", "pane" => "left"]);
    $this->add(["subject", "pane" => "left"]);
    $this->add(["from", "pane" => "left"]);
    $this->add(["from_name", "pane" => "left"]);
    $this->add(["cc", "input_type" => "textarea", "pane" => "right", "style" => "height:108px"]);
    $this->add(["bcc", "input_type" => "textarea", "pane" => "right", "style" => "height:108px"]);
    $this->add(["body", "input_type" => "textarea", "class" => "rich-text", "pane" => "body"]);
  }
}
