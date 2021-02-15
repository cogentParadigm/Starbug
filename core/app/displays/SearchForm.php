<?php
namespace Starbug\Core;

class SearchForm extends FormDisplay {
  public $method = "get";
  protected $panes = ["first" => "div.col-3", "second" => "div.col-3", "third" => "div.col-3", "fourth" => "div.col-3"];
  protected $filters = [];
  public function buildDisplay($options) {
    $this->buildLayout();
    $this->buildPrimaryControls();
    if (!empty($this->filters)) {
      $this->add(["filters", "input_type" => "html", "value" => "<a class=\"ml1 btn btn-default\" data-toggle=\"collapse\" href=\"#filterBox\" aria-expanded=\"false\" aria-controls=\"filterBox\"><span class=\"fa fa-filter\"></span></a>", "pane" => "keywords"]);
    }
    $this->buildFilters();
    foreach ($this->fields as $name => $field) {
      $this->update([$name, "required" => false, "data-filter" => $this->model, "data-filter-name" => $name, "id" => uniqid($name."_")]);
      if (!empty($field["from"])) {
        $this->update([$name, "input_type" => "text", "data-dojo-type" => "sb/form/MultipleSelect", "data-dojo-props" => "model: '".$field["from"]."', searchable: true"]);
      }
    }
    $this->actions->template = "inline";
    $this->actions->remove($this->defaultAction);
  }
  protected function buildLayout() {
    $this->layout->add(["top", "attributes" => ["class" => ["row"]], "keywords" => "div.flex.items-start"]);
    $this->layout->add(["bottom"] + $this->panes);
    $this->layout->update(["bottom", "attributes" => ["id" => "filterBox", "class" => ["collapse"]]]);
  }

  protected function buildPrimaryControls() {
    $this->add(["id", "input_type" => "text", "placeholder" => "Enter ID..", "nolabel" => true, "pane" => "keywords", "style" => "width:100px"]);
    $this->add(["keywords", "input_type" => "text", "placeholder" => "Search..", "nolabel" => true, "pane" => "keywords", "div" => "ml1", "style" => "width:250px"]);
  }

  protected function buildFilters() {
    foreach ($this->filters as $filter) {
      $this->add($filter);
    }
  }
}
