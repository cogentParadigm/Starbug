<?php
$sb->provide("core/lib/Display");
class Display {
	
	const HOOK_PHASE_BUILD = 0;
	const HOOK_PHASE_RENDER = 1;
	
	var $model;
	var $query;
	var $options = array();
	var $type = "default";
	var $dirty = false;
	
	var $fields = array();
	
	var $hooks = array();

	/**
	 * constructor. sets display name and options
	 * @param string $name the display name
	 * @param array $options the display options
	 */
	function __construct($model, $query, $options=array()) {
		$this->model = $model;
		$this->query = $query;
		$this->options = $options;
		$action = "display_".$this->query;
		sb($this->model)->$action($this, $options);
		$this->init($options);
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
	
	function build() {
		
	}
	
	/**
	 * render the display with the specified items
	 */
	function render() {
		$this->build();
		assign("display", $this);
		//assign("items", $items);
		render("display/".$this->type);
	}

}
?>
