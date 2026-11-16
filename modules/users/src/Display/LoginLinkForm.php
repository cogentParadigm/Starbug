<?php
namespace Starbug\Users\Display;

use Starbug\Display\FormDisplay;

class LoginLinkForm extends FormDisplay {
  public $model = "users";
  public $submit_label = "Send email";
  public function buildDisplay($options) {
    $queryParams = $this->request->getQueryParams();
    $this->add(["email", "default" => $options['email'] ?? $queryParams["email"] ?? ""]);
  }
}
