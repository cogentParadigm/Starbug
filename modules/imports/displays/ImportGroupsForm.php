<?php
namespace Starbug\Imports;

class ImportGroupsForm extends ImportsForm {
  public $model = "import_groups";
  public $cancel_url = "admin/import_groups";
  public $collection = "ImportGroupsForm";
  protected function buildDefault($options) {
    $this->add("name");
    $this->add(["imports", "input_type" => "text", "data-dojo-type" => "sb/form/MultipleSelect", "data-dojo-props" => "model:'imports'"]);
    $this->add(["source", "input_type" => "file_select"]);
    $this->actions->add(["saveRun", "label" => "Save and Import", "class" => "btn-success"]);
  }
  protected function buildRun($options) {
    $this->actions->remove($this->defaultAction);
    $source = $this->get("source");
    $output = $this->preparePaginatedOutput($source);
    if ($this->success("run")) {
      $this->add(["success", "input_type" => "html", "value" => '<p class="alert alert-success">Import completed</p>']);
    }
    $this->add(["table", "input_type" => "template", "value" => "csv-table.html", "class" => "table table-striped"] + $output);
    $this->add(["count", "input_type" => "html", "value" => "<p>".$output["pager"]->count." rows. Press import to begin.</p>"]);
    $this->actions->add(["run", "label" => "Import", "class" => "btn-success"]);
  }
}
