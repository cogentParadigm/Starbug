<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/form.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup form
 */
/**
 * @defgroup form
 * form utility
 * @ingroup util
 */
$sb->provide("util/form");
/**
 * Used to build XHTML forms
 * @ingroup form
 */
class form {
	/**
	 * @var string The name of the model, if this form submits to a model
	 */
	var $model;
	/**
	 * @var string The name of the function, if this form submits to a model
	 */
	var $action;
	/**
	 * @var string The URL to submit to
	 */
	var $url;
	/**
	 * @var string The submission method (get or post)
	 */
	var $method;
	/**
	 * @var string The URL to post back to if there is an error
	 */
	var $postback;
	/**
	 * @var string the rendering scope (see core/lib/Renderer)
	 */
	var $scope;

	var $schema = array();
	var $errors = array();

	/**
	 * constructor. initializes properties
	 * @param string $args a named parameter string with any initial values
	 */
	function __construct($args="") {
		global $request;
		$request_tag = array("term" => "form", "slug" => "form");
		if ((is_array($request->tags)) && (!in_array($request_tag, $request->tags))) $request->tags = array_merge($request->tags, array($request_tag));
		$args = star($args);
		efault($args['url'], $args['uri']);
		efault($args['url'], $_SERVER['REQUEST_URI']);
		efault($args['method'], "post");
		efault($args['postback'], $request->path);
		$this->model = $args['model'];
		$this->action = $args['action'];
		$this->url = $args['url'];
		$this->method = $args['method'];
		$this->postback = $args['postback'];
		if (!empty($this->model) && sb()->db->has($this->model)) {
			$schema = schema($this->model);
			$this->schema = $schema['fields'];
		}
		$this->errors = errors($this->model, true);
	}

	/**
	 * outputs the opening form tag and some hidden inputs
	 * @param string $atts attributes for the form tag
	 */
	function open($atts="") {
		if (!empty($atts)) $atts = $atts." ";
		if ($this->method == "post") $fields = (empty($this->model)) ? $_POST : $_POST[$this->model];
		else $fields = (empty( $this->model)) ? $_GET : $_GET[$this->model];
		assign("form", $this);
		assign("attributes", $atts);
		assign("model", $this->model);
		assign("url", $this->url);
		assign("method", $this->method);
		assign("postback", $this->postback);
		assign("action", $this->action);
		assign("fields", $fields);
		assign("errors", efault($this->errors, array()));
		render("form/open");
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
	function fill_ops(&$ops) {
		$ops = star($ops);
		$name = array_shift($ops);
		$ops['name'] = $name;
		//id, label, and class
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		$ops['nolabel'] = (isset($ops['nolabel'])) ? true : false;
		if (empty($ops['label'])) $ops['label'] = ucwords(str_replace("_", " ", $ops['name']));
		$ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ").$ops['name']."-field";
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
		$this->fill_ops($field);
		//run filters
		foreach (locate("form/".$control.".php", "filters") as $filter) include($filter);
		
		$capture = "field";
		$field['field'] = reset(explode("[", $field['name']));
		if ($control != "input" || $field['type'] != "file") $field['name'] = $this->get_name($field['name']);
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
