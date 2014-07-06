<?php
class FormDisplay {
	var $type = "form";
	var $template = "form";
	
	var $url;
	var $action;
	var $method;
	var $postback;
	var $errors = array();
	var $layout;
	function init($options) {
		// set default options
		if (empty($options['url'])) $options['url'] = empty($options['uri']) ? $_SERVER['REQUEST_URI'] : $options['uri'] ;
		if (empty($options['method'])) $options['method'] = 'post';
		if (empty($options['postback'])) $options['postback'] = request()->path;
		
		// assign options to properties
		$this->action = $options['action'];
		$this->url = $options['url'];
		$this->method = strtolower($options['method']);
		$this->postback = $options['postback'];
		
		// grab schema
		if (!empty($this->model) && sb()->db->has($this->model)) {
			$this->schema = sb($this->model)->hooks;
		}
		
		// grab errors
		$this->errors = errors($this->model, true);
		
		// set form attributes
		$this->attributes["action"] = $this->url;
		$this->attributes["method"] = $this->method;
		$this->attributes["accept-charset"] = "UTF-8";
		
		//create layout display
		$this->layout = build_display("layout", $this->model);
	}
	
	/**
	 * filter columns to set the input type and some other defaults
	 */
	function filter($field, $options, $column) {
		if (empty($options['input_type'])) $options['input_type'] = $column['input_type'];
		if ($column['input_type'] == "password") $options['class'] .= ((empty($options['class'])) ? "" : " ")."text";
		else if ($column['type'] == "bool") $options['value'] = 1;
		return $options;
	}
	
	/**
	 * override query function to only query with id
	 */
	function query($options=null) {
		//set options
		if (is_null($options)) $options = $this->options;
		
		if (empty($options['id'])) $this->items = array();
		else parent::query($options);
	}
	
	/**
	 * after the query is run and before the display is rendered
	 */
	function before_render() {
		//load $_POST
		if (!empty($this->items)) {
			if(empty($_POST[$this->model])) $_POST[$this->model] = array();
			foreach ($this->items[0] as $k => $v) if (!isset($_POST[$this->model][$k])) $_POST[$this->model][$k] = $v;
		}
	}
	
	/**
	 * get the full name attribute
	 * eg. name becomes users[name]
	 * eg. name[] becomes users[name][]
	 * @param string $name the relative name
	 * @return the full name
	 */
	function get_name($name) {
		if (empty($this->model)) return $name;
		else if (false !== strpos($name, "[")) {
			$parts = explode("[", $name, 2);
			return $this->model."[".$parts[0]."][".$parts[1];
		} else return $this->model."[".$name."]";
	}

	/**
	 * get the POST or GET value from the relative name
	 * @param string $name the relative name
	 * @return string the GET or POST value
	 */
	function get($name) {
		$parts = explode("[", $name);
		if ($this->method == "post") $var = (empty($this->model)) ? $_POST : $_POST[$this->model];
		else $var = (empty($this->model)) ? $_GET : $_GET[$this->model];
		foreach($parts as $p) $var = $var[rtrim($p, "]")];
		if (is_array($var)) return $var;
		else return stripslashes($var);
	}
	
	/**
	 * set the GET or POST value
	 * @param string $name the relative name
	 * @param string $value the value
	 */
	function set($name, $value) {
		$parts = explode("[", $name);
		$key = array_pop($parts);
		if (empty($this->model)) {
			if ($this->method == "post") $var = &$_POST;
			else $var = &$_GET;
		} else {
			if ($this->method == "post") $var = &$_POST[$this->model];
			else $var = &$_GET[$this->model];
		}
		foreach($parts as $p) {
			$var = &$var[rtrim($p, "]")];
		}
		$var[$key] = $value;
		return $value;
	}

	/**
	 * converts the option string given to form elements into an array and sets up default values
	 * @param star $ops the option string
	 */
	function fill_ops(&$ops, $control="") {
		$ops = star($ops);
		$name = array_shift($ops);
		$ops['name'] = $name;
		//id, label, and class
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		$ops['nolabel'] = (isset($ops['nolabel'])) ? true : false;
		if (empty($ops['label'])) $ops['label'] = ucwords(str_replace("_", " ", $ops['name']));
		$ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ").$ops['name']."-field";
		if (!in_array($control, array("checkbox", "radio"))) $ops['class'] .= " form-control";
	}

	/**
	 * generate a form control (a tag with a name attribute such as input, select, textarea, file)
	 * @param string $control the name of the form control, usually the tag (input, select, textarea, file)
	 * @param star $field the attributes for the html tag - special ones below
	 *									name: the relative name, eg. 'group[]' might become 'users[group][]'
	 *									content: the inner HTML of the tag if it is not self closing
	 * @param array $options an optional array that can be used to specify a data set eg. select options
	 * @param bool $self if true, will use a self closing tag. If false, will use an opening tag and a closing tag (default is false)
	 */
	function form_control($control, $field, $options=array()) {
		$this->fill_ops($field, $control);
		//run filters
		foreach (locate("form/".$control.".php", "filters") as $filter) include($filter);

		$capture = "field";
		$field['field'] = reset(explode("[", $field['name']));
		$field['name'] = $this->get_name($field['name']);
		foreach ($field as $k => $v) assign($k, $v);
		if (isset($field['nofield'])) {
			unset($field['nofield']);
			$capture = $control;
		}
		assign("attributes", $field);
		assign("control", $control);
		return capture(array($this->model."/form/$field[field]-$capture", "form/$field[field]-$capture", $this->model."/form/$capture", "form/$capture"));
	}
	
	function __call($name, $arguments) {
		efault($arguments[1], array());
		return $this->form_control($name, $arguments[0], $arguments[1]);
	}	
}
?>
