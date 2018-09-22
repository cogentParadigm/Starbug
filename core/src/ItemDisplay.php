<?php
namespace Starbug\Core;

class ItemDisplay extends Display {

  const HOOK_PHASE_BUILD = 0;
  const HOOK_PHASE_RENDER = 1;

  public $model = "";
  public $collection = "";

  public $fields = []; // fields to display (columns)
  public $items = []; // items to display (rows)

  protected $hooks = []; // active hooks
  protected $query; // database query object
  protected $hook_builder;
  protected $response;
  protected $models;
  protected $collections;

  public function __construct(TemplateInterface $output, ResponseInterface $response, ModelFactoryInterface $models, CollectionFactoryInterface $collections, HookFactoryInterface $hook_builder) {
    $this->output = $output;
    $this->models = $models;
    $this->collections = $collections;
    $this->response = $response;
    $this->hook_builder = $hook_builder;
  }

  /**
   * Allows you to filter the options for each column.
   * This is useful for adding defaults after the columns are set
   * or converting common parameters that have been specified to display specific parameters
   */
  public function filter($field, $options, $column) {
    return $options;
  }

  /**
   * Add field.
   */
  public function add($options) {
    $args = func_get_args();
    foreach ($args as $options) {
      if (!is_array($options)) $options = [$options];
      $field = array_shift($options);
      if (empty($options['model']) && !empty($this->model)) $options['model'] = $this->model;
      $target = empty($options['extend']) ? $field : $options['extend'];
      $column = [];
      if (!empty($options['model'])) {
        $base = $options['model'];
        while (!isset($this->models->get($base)->hooks[$target]) && !empty($this->models->get($base)->base)) {
          $base = $this->models->get($base)->base;
        }
        if (!empty($this->models->get($base)->hooks[$target])) $column = $this->models->get($base)->hooks[$target];
      }
      if (empty($column['label'])) $column['label'] = ucwords(str_replace('_', ' ', $field));
      if (empty($this->fields[$field])) $options = $this->filter($field, $options, $column);
      else $options = $this->filter($field, array_merge($this->fields[$field], $options), $column);
      foreach ($column as $hook => $value) {
        $this->invokeHook(self::HOOK_PHASE_BUILD, $hook, $field, $options, $column);
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

  public function invokeHook($phase, $hook, $field, &$options, $column) {

    if (!isset($this->hooks[$field."_".$hook])) $this->hooks[$field."_".$hook] = $this->hook_builder->get("display/".$hook);

    foreach ($this->hooks[$field."_".$hook] as $hook) {
      // hooks are invoked in 2 phases
      // 0 = build
      // 1 = render
      if ($phase == self::HOOK_PHASE_BUILD) {
        $hook->build($this, $field, $options, $column);
      } elseif ($phase == self::HOOK_PHASE_RENDER) {
        $hook->render($this, $field, $options, $column);
      }
    }
  }

  public function query($options = null) {
    if (is_null($options)) $options = $this->options;
    $collection = $this->collections->get($this->collection);
    $collection->setModel($this->model);
    $this->items = $collection->query($options);
  }

  /**
   * Render the display with the specified items.
   */
  public function render($query = true) {
    if ($query && !empty($this->model)) $this->query();
    parent::render();
  }
}
