<?php
class Display {

	const HOOK_PHASE_BUILD = 0;
	const HOOK_PHASE_RENDER = 1;

	public $model; //base model. eg. users
	public $name; //display name. eg. admin
	public $options = array(); //global options
	public $type = "default"; //display type
	public $template = "default"; // display template
	public $attributes = array("class" => array("display")); //attributes for top level node
	public $paged = false; //pagination enabled indicator
	public $pager; //pager object

	public $fields = array(); //fields to display (columns)
	public $items = array(); //items to display (rows)

	protected $hooks = array(); //active hooks
	protected $query; //database query object
	protected $dirty = false; //dirty indicator
	protected $context;
	protected $hook_builder;


	/**
	 * constructor. sets display name and options
	 * @param string $name the display name
	 * @param array $options the display options
	 */
	function __construct(TemplateInterface $context, HookFactoryInterface $hook_builder, $model=null, $name=null, $options=array()) {
		$this->context = $context;
		$this->hook_builder = $hook_builder;
		$this->model = $model;
		$this->name = $name;
		$this->options = $options;
		if (isset($options['template'])) $this->template = $options['template'];
		$this->attributes["class"][] = "display-type-".$this->type;
		$this->attributes["class"][] = "display-template-".$this->template;
		$this->init($options);
		if (!is_null($this->model) && !is_null($this->name)) {
			$action = "display_".$this->name;
			sb($this->model)->$action($this, $options);
		}
	}

	/**
	 * empty function to override. this is called right after construction
	 */
	function init($options) {

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
	 * option getter/setter
	 */
	function option($name, $value=null) {
		if (is_null($value)) return $this->options[$name];
		else $this->options[$name] = $value;
	}

	/**
	 * multiple option getter/setter
	 */
	function options($ops=array()) {
		$ops = star($ops);
		foreach ($ops as $k => $v) $this->option($k, $v);
	}

	/**
	 * mark dirty
	 */
	function dirty() {
		$this->dirty = true;
	}

	/**
	 * add field
	 */
	function add($options) {
		$args = func_get_args();
		foreach ($args as $options) {
			$options = star($options);
			$field = array_shift($options);
			efault($options['model'], $this->model);
			$target = empty($options['extend']) ? $field : $options['extend'];
			$base = $options['model'];
			while (!isset(sb($base)->hooks[$target]) && !empty(sb($base)->base)) {
				$base = sb($base)->base;
			}
			$column = sb($base)->hooks[$target];
			efault($column, array());
			if (empty($column['label'])) $column['label'] = format_label($field);
			if (empty($this->fields[$field])) $options = $this->filter($field, $options, $column);
			else $options = $this->filter($field, array_merge($this->fields[$field], $options), $column);
			foreach ($column as $hook => $value) {
				$this->invoke_hook(Display::HOOK_PHASE_BUILD, $hook, $field, $options, $column);
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
			if ($phase == Display::HOOK_PHASE_BUILD) {
				$hook->build($this, $field, $options, $column);
			} else if ($phase == Display::HOOK_PHASE_RENDER) {
				$hook->render($this, $field, $options, $column);
			}	
		}
	}

	function query($options=null, $model="") {
		if (empty($model)) $model = $this->model;

		//set options
		if (is_null($options)) $options = $this->options;

		//init query
		$this->query = entity_query($model);

		//search
		if (!empty($options['search'])) $this->query->search($options['search']);

		//limit
		if (!empty($options['limit'])) $this->query->limit($options['limit']);

		//pass to model
		$action_name = "query_".$this->name;
		$this->query = sb($model)->query_filters($this->name, $this->query, $options);
		$this->query = sb($model)->$action_name($this->query, $options);

		//page
		if (!empty($options['page'])) {
			$this->paged = true;
			$this->pager = $this->query->pager($options['page']);
		}

		$this->items = (property_exists($this->query, "data")) ? $this->query->data : $this->query->all();
		foreach($this->items as $idx => $item) {
			$this->items[$idx] = sb($this->model)->filter($item, $this->name);
		}
	}

	function before_render() {
		//extendable function
	}

	/**
	 * render the display with the specified items
	 */
	function render($query=true) {
		if ($query) $this->query();
		$this->before_render();
		$this->attributes["class"] = implode(" ", $this->attributes["class"]);
		$this->context->render("display/".$this->template, array("display" => $this));
	}

	/**
	 * capture the display with the specified items
	 */
	function capture($query=true) {
		if ($query) $this->query();
		$this->before_render();
		return $this->context->capture("display/".$this->template, array("display" => $this));
	}

}
?>
