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

	/**
	 * constructor. initializes properties
	 * @param string $args a named parameter string with any initial values
	 */
	function __construct($args="") {
		global $request;
		$request_tag = array("tag" => "form", "raw_tag" => "form");
		if ((is_array($request->tags)) && (!in_array($request_tag, $request->tags))) $request->tags = array_merge($request->tags, array($request_tag));
		$args = starr::star($args);
		efault($args['url'], $args['uri']);
		efault($args['url'], $_SERVER['REQUEST_URI']);
		efault($args['method'], "post");
		efault($args['postback'], $request->path);
		$this->model = $args['model'];
		$this->action = $args['action'];
		$this->url = $args['url'];
		$this->method = $args['method'];
		$this->postback = $args['postback'];
		$this->scope = $this->model."_".$this->action;
	}

	/**
	 * outputs the opening form tag and some hidden inputs
	 * @param string $atts attributes for the form tag
	 */
	function open($atts="") {
		if (!empty($atts)) $atts = $atts." ";
		if ($this->method == "post") $fields = (empty($this->model)) ? $_POST : $_POST[$this->model];
		else $fields = (empty( $this->model)) ? $_GET : $_GET[$this->model];
		$errors = errors($this->model);
		assign("attributes", $atts, $this->scope);
		assign("model", $this->model, $this->scope);
		assign("url", $this->url, $this->scope);
		assign("method", $this->method, $this->scope);
		assign("postback", $this->postback, $this->scope);
		assign("action", $this->action, $this->scope);
		assign("fields", $fields, $this->scope);
		assign("errors", efault($errors, array()), $this->scope);
		render("form/open", $this->scope);
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
	}


	function fill_ops(&$ops) {
		$ops = starr::star($ops);
		$name = array_shift($ops);
		$ops['name'] = $name;
		//id, label, and class
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = ucwords(str_replace("_", " ", $ops['name']));
		if (empty($ops['error'][$ops['name']])) $ops['error'][$ops['name']] = "This field is required.";
		$ops['class'] = (empty($ops['class'])) ? $ops['type'] : $ops['class']." ".$ops['type'];
		if ($ops['type'] == 'password') $ops['class'] .= " text";
		$ops['class'] .= " ".$ops['name']."-field";
	}

	/**
	 * generate a form control (a tag with a name attribute such as input, select, textarea, file)
	 * @param string $tag the name of the tag (input, select, textarea, file)
	 * @param star $ops the attributes for the html tag - special ones below
	 *									name: the relative name, eg. 'group[]' might become 'users[group][]'
	 *									content: the inner HTML of the tag if it is not self closing
	 * @param bool $self if true, will use a self closing tag. If false, will use an opening tag and a closing tag (default is false)
	 */
	function form_control($tag, $ops) {
		$capture = "field";
		$ops['field'] = reset(explode("[", $ops['name']));
		$ops['name'] = $this->get_name($ops['name']);
		foreach ($ops as $k => $v) assign($k, $v, $this->scope);
		if (isset($ops['nofield'])) {
			unset($ops['nofield']);
			$capture = $tag;
		}
		assign("attributes", $ops, $this->scope);
		assign("control", $tag, $this->scope);
		return capture(array($this->model."/form/$ops[field]-$capture", "form/$ops[field]-$capture", $this->model."/form/$capture", "form/$capture"), $this->scope);
	}

	function text($ops) {
		return $this->input("text", $ops);
	}

	function password($ops) {
		return $this->input("password", $ops);
	}

	function hidden($ops) {
		$ops = $ops."  nolabel:true";
		return $this->input("hidden", $ops);
	}

	function submit($ops="") {
		return $this->input("submit", "nolabel:".((empty($ops))? "" : "  ".$ops));
	}

	function button($label, $ops="") {
		$ops = star($ops);
		assign("label", $label, $this->scope);
		assign("attributes", $ops);
		return capture("form/button", $this->scope);
	}

	function file($ops) {
		$ops = $ops."  type:file";
		$this->fill_ops($ops);
		if (!empty($_POST[$ops['name']])) $ops['value'] = $_POST[$ops['name']];
		$ops['field'] = reset(explode("[", $ops['name']));
		foreach ($ops as $k => $v) assign($k, $v, $this->scope);
		assign("attributes", $ops, $this->scope);
		assign("control", "input", $this->scope);
		return capture("form/field", $this->scope);
	}

	function image($ops) {
		return $this->input("image", $ops);
	}

	function checkbox($ops) {
		$ops = $ops."  type:checkbox";
		$this->fill_ops($ops);
		if ($this->get($ops['name']) == $ops['value']) $ops['checked'] = 'checked';
		return $this->form_control("input", $ops);
	}

	function radio($ops) {
		return $this->input("radio", $ops);
	}

	function input($type, $ops) {
		$ops = $ops."  type:$type";
		$this->fill_ops($ops);
		//POSTed or default value
		$var = $this->get($ops['name']);
		if (!empty($var) && $type != "password") $ops['value'] = htmlentities($var);
		else if (!empty($ops['default'])) {
			$ops['value'] = $ops['default'];
			unset($ops['default']);
		}
		return $this->form_control("input", $ops);
	}

	function select($ops, $options=array()) {
		$this->fill_ops($ops);
		$value = $this->get($ops['name']);
		if ((empty($value)) && (!empty($ops['default']))) {
			$this->set($ops['name'], $ops['default']);
			unset($ops['default']);
		}
		if (!empty($ops['range'])) {
			$range = explode("-", $ops['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $options[$i] = $i;
			unset($ops['range']);
		}
		if (!empty($ops['caption'])) {
			if (!empty($ops['from'])) $options = query($ops['from']);
			$list = array();
			$keys = array();
			if (!empty($options)) foreach ($options[0] as $k => $v) if (false !== strpos($ops['caption'], "%$k%")) $keys[] = $k;
			foreach ($options as $o) {
				$cap = $ops['caption'];
				foreach($keys as $k) $cap = str_replace("%$k%", $o[$k], $cap);
				$list[$cap] = $o[$ops['value']];
			}
			$options = $list; unset($ops['caption']); unset($ops['value']);
		}
		assign("options", $options, $this->scope);
		return $this->form_control("select", $ops);
	}

	function date_select($ops) {
		global $sb;
		$sb->import("util/datepicker");
		$this->fill_ops($ops);
		//FILL VALUES FROM POST OR DEFAULT
		$name = $ops['name'];
		$value = $this->get($name);
		efault($value, $ops['default']);
		if ((!empty($value)) && (!is_array($value))) {
			$dt = strtotime($value);
			$this->set($name, array("year" => date("Y", $dt), "month" => date("m", $dt), "day" => date("d", $dt)));
			if (!empty($ops['time_select'])) $this->set($name."_time", array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt)));
		}
		//SETUP OPTION ARRAYS
		$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
		$day_options = array("Day" => "-1");
		for($i=1;$i<32;$i++) $day_options["$i"] = $i;		
		$start_year = ($ops['start_year']) ? $ops['start_year'] : date("Y");
		$end_year = ($ops['end_year']) ? $ops['end_year'] : $start_year+2;
		$year_options = array("Year" => "-1");
		if ($start_year < $end_year) for ($i=$start_year;$i<=$end_year;$i++) $year_options[$i] = $i;
		else for ($i=$start_year;$i>=$end_year;$i--) $year_options[$i] = $i;
		//BUILD SELECT BOXES
		$select = $this->select($name."[month]  id:".$ops['id']."-mm  nolabel:", $month_options);
		$select .= $this->select($name."[day]  id:".$ops['id']."-dd  nolabel:", $day_options);
		$select .= $this->select($name."[year]  id:".$ops['id']."  class:split-date range-low-".date("Y-m-d")." no-transparency  nolabel:", $year_options);
		//TIME
		if (!empty($ops['time_select'])) $select .= $this->time_select(array_merge(array($name), $ops));
		return $select;
	}

	function time_select($ops) {
		$this->fill_ops($ops);
		//GET POSTED OR DEFAULT VALUE
		$value = $this->get($ops['name']);
		efault($value, $ops['default']);
		if ((!empty($value)) && (!is_array($value))) {
			$dt = strtotime($value);
			$this->set($ops['name'], array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt)));
		}
		//SETUP OPTION ARRAYS
		$hour_options = array("Hour" => "-1");
		for($i=1;$i<13;$i++) $hour_options[$i] = $i;
		$minutes_options = array("Minutes" => "-1", "00" => "00", "15" => "15", "30" => "30", "45" => "45");
		$ampm_options = array("AM" => "am", "PM" => "pm");
		//BUILD SELECT BOXES
		$select  = $this->select($ops['name']."[hour]  id:".$ops['id']."-hour  nolabel:", $hour_options);
		$select .= $this->select($ops['name']."[minutes]  id:".$ops['id']."-minutes  nolabel:", $minutes_options);
		$select .= $this->select($ops['name']."[ampm]  id:".$ops['id']."  nolabel:", $ampm_options);
		return $select;
	}
	
	function textarea($ops) {
		$this->fill_ops($ops);
		efault($ops['cols'], "90");
		efault($ops['rows'], "90");
		//POSTed or default value
		$value = $this->get($ops['name']);
		if (!empty($ops['default'])) {
			efault($value, $ops['default']);
			unset($ops['default']);
		}
		efault($ops['value'], htmlentities($value));
		//name close
		return $this->form_control("textarea", $ops);
	}
	
}
?>
