<?php
namespace Starbug\Wizard;

class ViewEditFieldBaseForm extends ViewEditBaseForm {
  protected $viewStepTemplate = "wizard/field.html";
  protected $fieldName;
  protected $fieldOptions = [];
  public $cancelable = false;
  public $defaultAction = "save";
  protected function getDataForViewStep($options = []) {
    $data = parent::getDataForViewStep($options);
    $data["field"] = [
      "name" => $this->fieldName,
      "value" => $this->getDisplayValue($data)
    ] + $this->fieldOptions;
    return $data;
  }
  protected function editStep($options) {
    parent::editStep($options);
    $this->fieldOptions["nolabel"] = true;
    $this->addField($options);
    $this->actions->attributes["class"] = "flex flex-row-reverse-ns justify-end-ns";
    $this->addSaveButton($this->defaultAction, "Save", ["template" => "button/primary.html", "name" => "submit"]);
    $this->addBackButton($options["step"] - 1, "Cancel", ["class" => "mr2", "template" => "button/default.html"]);
  }
  protected function addField($options) {
    $this->add(array_merge([$this->fieldName], $this->fieldOptions));
  }
  protected function getDisplayValue($data) {
    return $data["data"][$this->fieldName];
  }
  protected function beforeQuery($options) {
    if ($this->hasPost() && !$this->failure()) {
      $this->setPost([]);
    }
  }
}
