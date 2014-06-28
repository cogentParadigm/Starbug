<?php
$sb->provide("core/lib/Display");
class Display {
	
	const HOOK_PHASE_BUILD = 0;
	const HOOK_PHASE_RENDER = 1;
	
	var $model; //base model. eg. users
	var $name; //display name. eg. admin
	var $query; //database query object
	var $options = array(); //global options
	
	var $type = "default"; //display type
	var $template = "default"; // display template
	var $attributes = array("class" => array("display")); //attributes for top level node
	var $dirty = false; //dirty indicator
	
	var $fields = array(); //fields to display (columns)
	var $items = array(); //items to display (rows)
	
	var $hooks = array(); //active hooks
	
	var $paged = false; //pagination enabled indicator
	var $pager; //pager object

	/**
	 * constructor. sets display name and options
	 * @param string $name the display name
	 * @param array $options the display options
	 */
	function __construct($model, $name, $options=array()) {
		$this->model = $model;
		$this->name = $name;
		$this->options = $options;
		if (isset($options['template'])) $this->template = $options['template'];
		$action = "display_".$this->name;
		$this->attributes["class"][] = "display-type-".$this->type;
		$this->attributes["class"][] = "display-template-".$this->template;
		$this->init($options);
		sb($this->model)->$action($this, $options);
	}
	
	/**
	 * empty function to override. this is called right after construction
	 */
	function init($options) {
	
	}

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
			$column = schema($options['model'].".fields.".$target);
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
	
	function insert($key, $options) {
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

		if (!isset($this->hooks[$field."_".$hook])) $this->hooks[$field."_".$hook] = build_hook("display/".$hook, "lib/DisplayHook", "core");
		$hook = $this->hooks[$field."_".$hook];
		
		//hooks are invoked in 2 phases
		//0 = build
		//1 = render
		if ($phase == Display::HOOK_PHASE_BUILD) {
			$hook->build($this, $field, $options, $column);
		} else if ($phase == Display::HOOK_PHASE_RENDER) {
			$hook->render($this, $field, $options, $column);
		}
	}
	
	function query($options=null) {
		//set options
		if (is_null($options)) $options = $this->options;
		
		//init query
		$this->query = query($this->model);
		
		//search
		if (!empty($options['search'])) $query->search($options['search']);
		
		//limit
		if (!empty($options['limit'])) $query->limit($options['limit']);

		//pass to model
		$action_name = "query_".$this->name;
		$query = sb($this->model)->query_filters($this->name, $this->query, $options);
		$query = sb($this->model)->$action_name($this->query, $options);

		//page
		if (!empty($options['page'])) {
			$this->paged = true;
			$this->pager = $this->query->pager($options['page']);
		}
		
		$this->items = (property_exists($this->query, "data")) ? $query->data : $query->all();
	}
	
	/**
	 * render the display with the specified items
	 */
	function render($query=true) {
		if ($query) $this->query();
		$this->attributes["class"] = implode(" ", $this->attributes["class"]);
		assign("display", $this);
		//assign("items", $items);
		render("display/".$this->template);
	}
	
	/**
	 * capture the display with the specified items
	 */
	function capture($query=true) {
		if ($query) $this->query();
		assign("display", $this);
		//assign("items", $items);
		return capture("display/".$this->template);
	}

}
?>
