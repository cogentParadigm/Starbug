<?php
class FormDisplay extends ItemDisplay {
	public $type = "form";
	public $template = "form";
	public $action = "form";

	public $url;
	public $method = "post";
	public $errors = array();
	public $layout;
	public $default_action = "create";
	public $submit_label = "Save";
	public $cancel_url = "";
	public $actions;
	protected $vars = array();

	function build($options) {
		$this->options = $options;
		// grab schema
		if (!empty($this->model) && sb()->models->has($this->model)) {
			$this->schema = sb($this->model)->hooks;
		}

		//create layout display
		$this->layout = $this->output->build_display("LayoutDisplay");
		//create actions display
		$this->actions = $this->output->build_display("ItemDisplay");
		$this->actions->add($this->default_action."  label:".$this->submit_label."  class:btn-success");

		//run query
		$this->before_query($options);
		$this->query();
		$this->build_display($options);
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function before_query($options) {
		//override this method if needed
	}

	/**
	 * filter columns to set the input type and some other defaults
	 */
	function filter($field, $options, $column) {
		if (empty($options['input_type'])) $options['input_type'] = $column['input_type'];
		if ($options['input_type'] == "password") $options['class'] .= ((empty($options['class'])) ? "" : " ")."text";
		else if ($column['type'] == "bool") $options['value'] = 1;
		else if ($options['input_type'] == "datetime") $options['data-dojo-type'] = "starbug/form/DateTextBox";
		else if ($options['input_type'] == "crud") {
			if (empty($options['table'])) $options['table'] = (empty($column['table'])) ? $options['model']."_".$field : $column['table'];
		} else if ($options['input_type'] == "category_select") {
				if (empty($options['taxonomy'])) $options['taxonomy'] = (empty($column['taxonomy'])) ? $options['model']."_".$field : $column['taxonomy'];
		}
		if (!isset($options['required'])) {
			$default = isset($column['default']);
			$optional = isset($column['optional']);
			$nullable = isset($column['null']);
			$not_optional_update = (!isset($column['optional_update']) || empty($object_id));
			if (!$default && !$optional && !$nullable && $not_optional_update) {
					$options['required'] = true;
			} else {
				$options['required'] = false;
			}
		}
		return $options;
	}

	/**
	 * override query function to only query with id
	 */
	function query($options = null) {
		//set options
		if (is_null($options)) $options = $this->options;

		if (empty($options['id'])) $this->items = array();
		else parent::query(array("action" => $this->default_action) + $options);

		//load $_POST
		if (!empty($this->items)) {
			if (empty($_POST[$this->model])) $_POST[$this->model] = array();
			foreach ($this->items[0] as $k => $v) if (!isset($_POST[$this->model][$k])) $_POST[$this->model][$k] = $v;
		}
	}

	function before_render() {
		// set form attributes
		$this->attributes["action"] = $this->url;
		$this->attributes["method"] = $this->method;
		$this->attributes["accept-charset"] = "UTF-8";
		if (!empty($this->model) && !empty($this->default_action)) {
			if (success($this->model, $this->default_action)) $this->attributes['class'][] = "submitted";
			else if (failure($this->model, $this->default_action)) $this->attributes['class'][] = "errors";
		}
		// grab errors and update schema
		$this->errors = array();
		foreach ($this->fields as $name => $field) {
			$this->schema[$name] = column_info($field['model'], $name);
			$errors = errors($this->get_name($name, $this->schema[$name]['entity']), true);
			if (!empty($errors)) $this->errors[$name] = $errors;
		}
	}

	function render($query = false) {
		parent::render($query);
	}

	/**
	 * get the full name attribute
	 * eg. name becomes users[name]
	 * eg. name[] becomes users[name][]
	 * @param string $name the relative name
	 * @return the full name
	 */
	function get_name($name, $model = "") {
		if (empty($model)) {
			$model = (empty($this->fields[$name])) ? $this->model : $this->fields[$name]["model"];
		}
		if (empty($model) || $this->method == "get") return $name;
		else if (false !== strpos($name, "[")) {
			$parts = explode("[", $name, 2);
			return $model."[".$parts[0]."][".$parts[1];
		} else return $model."[".$name."]";
	}

	/**
	 * get the POST or GET value from the relative name
	 * @param string $name the relative name
	 * @return string the GET or POST value
	 */
	function get($name, $model = "") {
		if (empty($model)) {
			$model = (empty($this->fields[$name])) ? $this->model : $this->fields[$name]["model"];
		}
		$parts = explode("[", $name);
		if ($this->method == "post") $var = (empty($model)) ? $_POST : $_POST[$model];
		else $var = $_GET;
		foreach ($parts as $p) $var = $var[rtrim($p, "]")];
		if (is_array($var)) return $var;
		else return stripslashes($var);
	}

	/**
	 * set the GET or POST value
	 * @param string $name the relative name
	 * @param string $value the value
	 */
	function set($name, $value, $model = "") {
		if (empty($model)) {
			$model = (!empty($this->fields[$name])) ? $this->model : $this->fields[$name]["model"];
		}
		$parts = explode("[", $name);
		$key = array_pop($parts);
		if (empty($model)) {
			if ($this->method == "post") $var = &$_POST;
			else $var = &$_GET;
		} else {
			if ($this->method == "post") $var = &$_POST[$model];
			else $var = &$_GET[$model];
		}
		foreach ($parts as $p) {
			$var = &$var[rtrim($p, "]")];
		}
		$var[$key] = $value;
		return $value;
	}

	/**
	 * converts the option string given to form elements into an array and sets up default values
	 * @param star $ops the option string
	 */
	function fill_ops(&$ops, $control = "") {
		$ops = star($ops);
		$name = array_shift($ops);
		if (empty($ops['name'])) $ops['name'] = $name;
		//model
		if (empty($ops['model'])) $ops['model'] = $this->model;
		//id, label, and class
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		$ops['nolabel'] = (isset($ops['nolabel'])) ? true : false;
		if (empty($ops['label'])) $ops['label'] = ucwords(str_replace("_", " ", $ops['name']));
		$ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ").$ops['name']."-field";
		if (!in_array($control, array("checkbox", "radio"))) $ops['class'] .= " form-control";
	}

	function assign($key, $value = null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) $this->assign($k, $v);
		} else {
			$this->vars[$key] = $value;
		}
	}

	/**
	 * generate a form control (a tag with a name attribute such as input, select, textarea, file)
	 * @param string $control the name of the form control, usually the tag (input, select, textarea, file)
	 * @param star $field the attributes for the html tag - special ones below
	 *									name: the relative name, eg. 'group[]' might become 'users[group][]'
	 *									content: the inner HTML of the tag if it is not self closing
	 * @param bool $self if true, will use a self closing tag. If false, will use an opening tag and a closing tag (default is false)
	 */
	function form_control($control, $field) {
		$this->vars = array("display" => $this);
		$this->fill_ops($field, $control);
		//run filters
		foreach ($this->output->locator->locate("form/".$control.".php", "filters") as $filter) include($filter);

		$capture = "field";
		if (empty($field['field'])) $field['field'] = reset(explode("[", $field['name']));
		$field['name'] = $this->get_name($field['name'], $field['model']);
		foreach ($field as $k => $v) $this->assign($k, $v);
		if (isset($field['nofield'])) {
			unset($field['nofield']);
			$capture = $control;
		}
		$this->assign("attributes", $field);
		$this->assign("control", $control);
		return $this->output->capture(array($field['model']."/form/$field[field]-$capture", "form/$field[field]-$capture", $field['model']."/form/$capture", "form/$capture"), $this->vars);
	}

	function __call($name, $arguments) {
		efault($arguments[1], array());
		return $this->form_control($name, $arguments[0], $arguments[1]);
	}
}
?>
