<?php
namespace Starbug\Wizard;

abstract class ViewEditBaseForm extends FormWizard {
  protected array $steps = [
    1 => "viewStep",
    2 => "editStep"
  ];
  protected $viewStepTemplate;
  protected $showSuccessMessage = false;
  protected function getDataForViewStep($options = []) {
    return ["data" => $this->getPost(), "options" => $options];
  }
  protected function viewStep($options) {
    $this->add([
      "view",
      "input_type" => "html",
      "value" => $this->output->capture($this->viewStepTemplate, $this->getDataForViewStep($options))
    ]);
  }
  protected function editStep($options) {
    $this->update(["step", "default" => 1]);
  }
}
