<?php
namespace Starbug\Core;

class ItemDisplay extends Display {

  public $model = "";
  public $collection = "";

  public $fields = []; // fields to display (columns)
  public $items = []; // items to display (rows)

  protected $query; // database query object
  protected $collections;

  public function __construct(TemplateInterface $output, CollectionFactoryInterface $collections) {
    $this->output = $output;
    $this->collections = $collections;
  }

  /**
   * Allows you to filter the options for each column.
   * This is useful for adding defaults after the columns are set
   * or converting common parameters that have been specified to display specific parameters
   */
  public function filter($field, $options) {
    return $options;
  }

  /**
   * Add field.
   */
  public function add($options) {
    $args = func_get_args();
    foreach ($args as $options) {
      if (!is_array($options)) {
        $options = [$options];
      }
      $field = array_shift($options);
      if (empty($options['model']) && !empty($this->model)) {
        $options['model'] = $this->model;
      }
      if (!isset($options['label'])) {
        $options['label'] = ucwords(str_replace('_', ' ', $field));
      }
      if (empty($this->fields[$field])) {
        $options = $this->filter($field, $options);
      } else {
        $options = $this->filter($field, array_merge($this->fields[$field], $options));
      }
      $this->fields[$field] = $options;
    }
  }

  /**
   * Insert a field at a specific index.
   *
   * @param int $index
   * @param star $options
   */
  public function insert($index, $options) {
    $args = func_get_args();
    $index = array_shift($args);
    $before = array_slice($this->fields, 0, $index, true);
    $after = array_slice($this->fields, $index, count($this->fields), true);
    $this->fields = [];
    call_user_func_array([$this, 'add'], $args);
    $this->fields = $before + $this->fields + $after;
  }

  public function update($options) {
    $this->add($options);
  }

  /**
   * Remove field
   */
  public function remove($field) {
    unset($this->fields[$field]);
  }

  public function query($options = null) {
    if (is_null($options)) {
      $options = $this->options;
    }
    $collection = $this->collections->get($this->collection);
    $collection->setModel($this->model);
    $this->items = $collection->query($options);
  }

  /**
   * Render the display with the specified items.
   */
  public function render($query = true) {
    if ($query && !empty($this->model)) {
      $this->query();
    }
    parent::render();
  }
}
