<?php
namespace Starbug\Core;

class LayoutDisplay extends ItemDisplay {
  public $type = "layout";
  public $template = "layout.html";
  public $cells = [];
  public $lastCell = false;

  /**
   * Allows you to filter the options for each column.
   * This is useful for adding defaults after the columns are set
   * or converting common parameters that have been specified to display specific parameters
   */
  public function filter($field, $options) {
    foreach ($options as $k => $v) {
      if ($k !== 'attributes') {
        $this->cells[$k] = Renderable::create($v);
        $this->lastCell = $k;
      }
    }
    if (!isset($options['attributes']['class'])) $options['attributes']['class'] = ['row'];
    elseif (!in_array('row', $options['attributes']['class'])) $options['attributes']['class'][] = 'row';
    return $options;
  }

  public function query($options = null) {
    // disable query
  }

  public function put($parent, $selector, $content = "", $key = "") {
    if (!isset($this->cells[$parent])) {
      $key = $content;
      $content = $selector;
      $selector = $parent;
      $parent = null;
      $node = Renderable::create($selector, $content);
    } else {
      $node = Renderable::create($this->cells[$parent], $selector, $content);
    }
    if (!empty($key)) {
      $this->cells[$key] = $node;
      $this->lastCell = $key;
    }
    return $node;
  }

  public function append($parent, $html) {
    if (empty($parent)) $parent = $this->lastCell;
    else $this->lastCell = $parent;
    $this->cells[$parent]->appendChild($html);
  }

  public function isEmpty() {
    return empty($this->cells);
  }
}
