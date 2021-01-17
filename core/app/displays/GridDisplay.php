<?php
namespace Starbug\Core;

use Starbug\Js\DojoConfiguration;

class GridDisplay extends ItemDisplay {
  public $type = "grid";
  public $template = "grid.html";
  public $grid_class = "starbug/grid/PagedGrid";
  public $dnd = false;
  public $action = "";
  public $fields = [
    "row_options" => ["field" => "id", "label" => "Options", "class" => "field-options", "plugin" => "starbug.grid.columns.options"]
  ];

  public function __construct(TemplateInterface $output, CollectionFactoryInterface $collections, DojoConfiguration $dojo) {
    $this->output = $output;
    $this->collections = $collections;
    $this->dojo = $dojo;
  }

  public function build($options = []) {
    // set defaults
    if (!empty($options['attributes'])) $this->attributes = $options['attributes'];
    $this->options = $options;
    if (!empty($options['dnd'])) $this->dnd();
    $this->buildDisplay($options);
  }

  public function dnd() {
    $this->dnd = true;
    $this->grid_class = "starbug/grid/DnDGrid";
    $this->fields = array_merge(['dnd' => ["field" => "id", "label" => "-", "class" => "field-drag",  "plugin" => "starbug.grid.columns.handle", "sortable" => false]], $this->fields);
  }

  public function columnAttributes($field, $options) {
    if (empty($options["field"])) $options["field"] = $field;
    $options['data-dgrid-column'] = [];
    if (!empty($options['editor']) && empty($options['editOn'])) {
      $options['editOn'] = "'dblclick'";
    }
    foreach ($options as $k => $v) {
      if (!in_array($k, ["id", "class", "style", "label", "data-dgrid-column", "plugin"]) && $v !== "") {
        if ($k == "model" || $k == "field" || ($k == "default" && !is_numeric($v))) $v = "'".$v."'";
        elseif ($v === false) $v = "false";
        $options['data-dgrid-column'][] = $k.":".$v;
      }
    }
    $options['data-dgrid-column'] = '{'.implode(', ', $options['data-dgrid-column']).'}';
    if (isset($options['plugin']) && !isset($options['readonly'])) {
      $this->dojo->addDependency(str_replace(".", "/", $options['plugin']));
      $options['data-dgrid-column'] = $options['plugin']."(".$options['data-dgrid-column'].")";
    }
    return $options;
  }

  public function query($options = null) {
    // defer query responsibilities to dgrid
  }

  public function beforeRender() {
    $this->attributes['model'] = $this->model;
    $this->attributes['class'][] = "dgrid-autoheight dbootstrap-grid";
    if (empty($this->attributes['id'])) $this->attributes['id'] = $this->model."_grid";
    if (empty($this->attributes['data-dojo-id'])) $this->attributes['data-dojo-id'] = $this->attributes['id'];
    $this->attributes['action'] = $this->action;

    // build data-dojo-props attribute
    foreach ($this->attributes as $k => $v) {
      if (!in_array($k, ["id", "class", "style", "data-dojo-type", "data-dojo-props", "data-dojo-id"])) {
        $this->attributes['data-dojo-props'][$k] = $v;
      }
    }
    $this->dojo->addDependency($this->grid_class);
    $this->attributes['data-dojo-type'] = $this->grid_class;
    // convert from array to string
    $this->attributes['data-dojo-props'] = trim(str_replace('"', "'", json_encode($this->attributes['data-dojo-props'])), '{}');
    // add query params
    $params = $this->options;
    foreach ($params as $key => $value) if (is_array($value)) $params[$key] = implode(",", $value);
    if (!empty($params)) {
      $this->attributes['data-dojo-props'] .= ', query: {';
      foreach ($params as $k => $v) $this->attributes['data-dojo-props'] .= $k.":'".$v."', ";
      $this->attributes['data-dojo-props'] = rtrim($this->attributes['data-dojo-props'], ', ').'}';
    }
    $row_options = $this->fields['row_options'];
    unset($this->fields['row_options']);
    $this->fields['row_options'] = $row_options;
  }
}
