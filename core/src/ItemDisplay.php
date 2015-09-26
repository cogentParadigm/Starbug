<?php
namespace Starbug\Core;
class ItemDisplay extends Display {

	const HOOK_PHASE_BUILD = 0;
	const HOOK_PHASE_RENDER = 1;

	public $model = "";
	public $action = "";

	public $fields = array(); //fields to display (columns)
	public $items = array(); //items to display (rows)

	protected $hooks = array(); //active hooks
	protected $query; //database query object
	protected $hook_builder;
	protected $response;
	protected $models;

	function __construct(TemplateInterface $output, Response $response, ModelFactoryInterface $models, HookFactoryInterface $hook_builder) {
		$this->output = $output;
		$this->models = $models;
		$this->response = $response;
		$this->hook_builder = $hook_builder;
	}

	/**
	 * Allows you to filter the options for each column.
	 * This is useful for adding defaults after the columns are set
	 * or converting common parameters that have been specified to display specific parameters
	 */
	function filter($field, $options, $column) {
		return $options;
	}

	/**
	 * add field
	 */
	function add($options) {
		$args = func_get_args();
		foreach ($args as $options) {
			$options = star($options);
			$field = array_shift($options);
			if (empty($options['model']) && !empty($this->model)) $options['model'] = $this->model;
			$target = empty($options['extend']) ? $field : $options['extend'];
			$column = array();
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
				$this->invoke_hook(self::HOOK_PHASE_BUILD, $hook, $field, $options, $column);
			}
			$this->fields[$field] = $options;
		}
	}

	/**
	 * insert a field at a specific index
	 * @param int $index
	 * @param star $options
	 */
	function insert($index, $options) {
		$args = func_get_args();
		$index = array_shift($args);
		$before = array_slice($this->fields, 0, $index, true);
		$after = array_slice($this->fields, $index, count($this->fields), true);
		$this->fields = array();
		call_user_func_array(array($this, 'add'), $args);
		$this->fields = $before + $this->fields + $after;

	}

	function update($options) {
		$this->add($options);
	}

	/**
	 * remove field
	 */
	function remove($field) {
		unset($this->fields[$field]);
	}

	function invoke_hook($phase, $hook, $field, &$options, $column) {

		if (!isset($this->hooks[$field."_".$hook])) $this->hooks[$field."_".$hook] = $this->hook_builder->get("display/".$hook);

		foreach ($this->hooks[$field."_".$hook] as $hook) {
			//hooks are invoked in 2 phases
			//0 = build
			//1 = render
			if ($phase == self::HOOK_PHASE_BUILD) {
				$hook->build($this, $field, $options, $column);
			} else if ($phase == self::HOOK_PHASE_RENDER) {
				$hook->render($this, $field, $options, $column);
			}
		}
	}

	function query($options=null, $model="") {
		if (empty($model)) $model = $this->model;

		//set options
		if (is_null($options)) $options = $this->options;

		//init query
		$this->query = $this->models->get($model)->query();

		//search
		if (!empty($options['search'])) $this->query->search($options['search']);

		//limit
		if (!empty($options['limit'])) $this->query->limit($options['limit']);

		$this->query = $this->build_query($this->query, $options);
		if (!empty($model)) {
			$action_name = "query_".$this->action;
			$this->query = $this->models->get($model)->query_filters($this->action, $this->query, $options);
			$this->query = $this->models->get($model)->$action_name($this->query, $options);
		}

		//page
		if (!empty($options['page'])) {
			$this->assign('paged', true);
			$this->assign('pager', $this->query->pager($options['page']));
		}

		$this->items = (property_exists($this->query, "data")) ? $this->query->data : $this->query->all();
		if (!empty($model)) {
			foreach($this->items as $idx => $item) {
				$this->items[$idx] = $this->models->get($model)->filter($item, $this->action);
			}
		}
	}

	function build_query($query, &$ops) {
		return $query;
	}

	/**
	 * render the display with the specified items
	 */
	function render($query=true) {
		if ($query && !empty($this->model)) $this->query();
		parent::render();
	}

}
?>
