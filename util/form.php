<?php
/**
* FILE: util/form.php
* PURPOSE: Form generation utility
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
$sb->provide("util/form");
class form {
	var $model;
	var $action;
	var $url;
	var $method;
	var $postback;
	function form($args="") {
		global $request;
		$request->tags = array_merge($request->tags, array(array("tag" => "form", "raw_tag" => "form")));
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
	}

	function open($atts="") {
		$open = '<form'.(($atts) ? " ".$atts : "").' action="'.$this->url.'" method="'.$this->method.'">'."\n";
		if (!empty($this->action)) $open .= '<input class="action" name="action['.$this->model.']" type="hidden" value="'.$this->action.'" />'."\n";
		if (!empty($_POST[$this->model]['id'])) $open .= '<input id="id" name="'.$this->model.'[id]" type="hidden" value="'.$_POST[$this->model]['id'].'" />'."\n";
		return $open;
	}
	
	function get_name($name) {
		if (empty($this->model)) return $name;
		else if (false !== strpos($name, "[")) {
			$parts = explode("[", $name, 2);
			return $this->model."[".$parts[0]."][".$parts[1];
		} else return $this->model."[".$name."]";
	}

	function get($name) {
		$parts = explode("[", $name);
		if ($this->method == "post") $var = (empty($this->model)) ? $_POST : $_POST[$this->model];
		else $var = (empty($this->model)) ? $_GET : $_GET[$this->model];
		foreach($parts as $p) {
			$var = $var[rtrim($p, "]")];
		}
		return $var;
	}
	
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
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		if (empty($ops['error'][$ops['name']])) $ops['error'][$ops['name']] = "This field is required.";
		$ops['class'] = (empty($ops['class'])) ? $ops['type'] : $ops['class']." ".$ops['type'];
	}

	function label(&$ops) {
		global $sb;
		$name = reset(explode("[", $ops['name']));
		if (!isset($ops['nolabel'])) $lab = '<label for="'.$ops['id'].'"'.((empty($ops['identifier_class'])) ? '' : ' class="'.$ops['identifier_class'].'"').'>'.$ops['label']."</label>";
		else unset($ops['nolabel']);
		if (isset($sb->errors[$this->model][$name])) foreach($sb->errors[$this->model][$name] as $err => $message) $lab .= "\n"."<span class=\"error\">".((!empty($ops['error'][$err])) ? $ops['error'][$err] : $message)."</span>";
		unset($ops['label']);
		unset($ops['error']);
		return $lab;
	}
	
	function form_control($tag, $ops, $self=false) {
		$ops['name'] = $this->get_name($ops['name']);
		$ops = array_merge(array($tag), $ops);
		return $this->tag($ops, $self);
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
		return $this->tag("input  type:submit".((empty($ops))? "" : "  ".$ops), true);
	}

	function bin($ops) {
		return $this->input("file", $ops);
	}

	function image($ops) {
		return $this->input("image", $ops);
	}

	function checkbox($ops) {
		$ops = $ops."  type:checkbox";
		$this->fill_ops($ops);
		$input = $this->label($ops)."\n";
		if ($this->get($ops['name']) == $ops['value']) $ops['checked'] = 'checked';
		return $input.$this->form_control("input", $ops, true);
	}

	function radio($ops) {
		return $this->input("radio", $ops);
	}

	function input($type, $ops) {
		$ops = $ops."  type:$type";
		$this->fill_ops($ops);
		$input = $this->label($ops)."\n";
		//POSTed or default value
		$var = $this->get($ops['name']);
		if (!empty($var)) $ops['value'] = $var;
		else if (!empty($ops['default'])) {
			$ops['value'] = $ops['default'];
			unset($ops['default']);
		}
		return $input.$this->form_control("input", $ops, true);
	}

	function select($ops, $options=array()) {
		$this->fill_ops($ops);
		$select = $this->label($ops)."\n";
		$var = $this->get($ops['name']);
		if ((empty($var)) && (!empty($ops['default']))) {
			$this->set($ops['name'], $ops['default']);
			unset($ops['default']);
		}
		if (!empty($ops['range'])) {
			$range = split(":", $ops['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $options[$i] = $i;
			unset($ops['range']);
		}
		$ops['content'] = "";
		foreach ($options as $caption => $val) $ops['content'] .= "<option value=\"$val\"".(($this->get($ops['name']) == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		return $select.$this->form_control("select", $ops);
	}

	function date_select($ops) {
		$this->fill_ops($ops);
		$select = $this->label($ops)."\n";
		//FILL VALUES FROM POST OR DEFAULT
		$name = $ops['name'];
		$value = $this->get($name);
		efault($value, $ops['default']);
		if (!empty($value)) {
			$dt = strtotime($value);
			$this->set($name, array("year" => date("Y", $dt), "month" => date("m", $dt), "day" => date("d", $dt)));
			if (!empty($ops['time_select'])) $this->set($name."_time", array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt)));
		}
		//SETUP OPTION ARRAYS
		$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
		$day_options = array("Day" => "-1");
		for($i=1;$i<32;$i++) $day_options["$i"] = $i;		
		$year = date("Y");
		$year_options = array("Year" => "-1", $year => $year, (((int) $year)+1) => (((int) $year)+1));
		//BUILD SELECT BOXES
		$select .= $this->select($name."[month]  id:".$ops['id']."-mm  nolabel:", $month_options);
		$select .= $this->select($name."[day]  id:".$ops['id']."-dd  nolabel:", $day_options);
		$select .= $this->select($name."[year]  id:".$ops['id']."  class:split-date range-low-".date("Y-m-d")." no-transparency  nolabel:", $year_options);
		//TIME
		if (!empty($ops['time_select'])) $select .= $this->time_select(array_merge(array($name), $ops));
		return $select;
	}

	function time_select($ops) {
		$this->fill_ops($ops);
		$select = $this->label($ops)."\n";
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
		$select .= $this->select($ops['name']."[hour]  id:".$ops['id']."-hour  nolabel:", $hour_options);
		$select .= $this->select($ops['name']."[minutes]  id:".$ops['id']."-minutes  nolabel:", $minutes_options);
		$select .= $this->select($ops['name']."[ampm]  id:".$ops['id']."  nolabel:", $ampm_options);
		return $select;
	}
	
	function textarea($ops) {
		$this->fill_ops($ops);
		$input = $this->label($ops)."\n";
		efault($ops['cols'], "90");
		efault($ops['rows'], "90");
		//POSTed or default value
		$value = $this->get($ops['name']);
		if (!empty($ops['default'])) {
			efault($value, $ops['default']);
			unset($ops['default']);
		}
		efault($ops['content'], $value);
		//name close
		return $input.$this->form_control("textarea", $ops);
	}

	function tag($tag, $self=false) {
		if (is_array($tag)) $name = array_shift($tag);
		else {
			$parts = explode("  ", $tag, 2);
			$name = $parts[0];
			if (count($parts) > 1) $tag = starr::star($parts[1]);
		}
		$content = $tag['content']; unset($tag['content']); $str = "";
		foreach($tag as $key => $value) $str .= " $key=\"$value\"";
		return ($self) ? "<$name$str />" : "<$name$str>$content</$name>";
	}
	
}
