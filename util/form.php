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
	function form($postvar, $args="") {
		$args = starr::star($args);
		efault($args['url'], $_SERVER['REQUEST_URI']);
		efault($args['method'], "post");
		$this->model = $postvar;
		$this->action = $args['action'];
		$this->url = $args['url'];
		$this->method = $args['method'];
	}

	function open($atts="") {
		$open = '<form'.(($atts) ? " ".$atts : "").' action="'.$this->url.'" method="'.$this->method.'">'."\n";
		$open .= '<input class="action" name="action['.$this->model.']" type="hidden" value="'.$this->action.'" />'."\n";
		if (!empty($_POST[$this->model]['id'])) $open .= '<input id="id" name="'.$this->model.'[id]" type="hidden" value="'.$_POST[$this->model]['id'].'" />'."\n";
		return $open;
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
		if (!isset($ops['nolabel'])) $lab = '<label for="'.$ops['id'].'"'.((empty($ops['identifier_class'])) ? '' : ' class="'.$ops['identifier_class'].'"').'>'.$ops['label']."</label>";
		else unset($ops['nolabel']);
		if (isset($sb->errors[$this->model][$ops['name']])) foreach($sb->errors[$this->model][$ops['name']] as $err => $message) $lab .= "\n"."<span class=\"error\">".((!empty($ops['error'][$err])) ? $ops['error'][$err] : $message)."</span>";
		unset($ops['label']);
		unset($ops['error']);
		return $lab;
	}
	
	function form_control($tag, $ops, $self=false) {
		$ops['name'] = $this->model."[".$ops['name']."]";
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
		return $this->input("hidden", $ops."  nolabel:true");
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
		$this->fill_ops($ops."  type:checkbox");
		$input = $this->label($ops)."\n";
		if ($_POST[$this->model][$ops['name']] == $ops['value']) $ops['checked'] = 'checked';
		return $input.$this->form_control("input", $ops, true);
	}

	function radio($ops) {
		return $this->input("radio", $ops);
	}

	function input($type, $ops) {
		$this->fill_ops($ops."  type:$type");
		$input = $this->label($ops)."\n";
		//POSTed or default value
		if (!empty($_POST[$this->model][$ops['name']])) $ops['value'] = $_POST[$this->model][$ops['name']];
		else if (!empty($ops['default'])) {
			$ops['value'] = $ops['default'];
			unset($ops['default']);
		}
		return $input.$this->form_control("input", $ops, true);
	}

	function select($ops, $options=array()) {
		$this->fill_ops($ops);
		$select = $this->label($ops)."\n";
		if ((empty($_POST[$this->model][$ops['name']])) && (!empty($ops['default']))) {
			$_POST[$this->model][$ops['name']] = $ops['default'];
			unset($ops['default']);
		}
		if (!empty($ops['range'])) {
			$range = split(":", $ops['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $options[$i] = $i;
			unset($ops['range']);
		}
		$ops['content'] = "";
		foreach ($options as $caption => $val) $ops['content'] .= "<option value=\"$val\"".(($_POST[$this->model][$ops['name']] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		return $select.$this->form_control("select", $ops);
	}

	function date_select($ops) {
		$ops = starr::star($ops);
		$name = array_shift($ops);
		$ops['name'] = $name;
		//SETUP OPTION ARRAYS
		$year = date("Y");
		$year_options = array("Year" => "-1", $year => $year, (((int) $year)+1) => (((int) $year)+1));
		$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
		$day_options = array("Day" => "-1");
		for($i=1;$i<32;$i++) $day_options["$i"] = $i;
		//ID, NAME, LABEL, ERRORS
		if (!empty($_POST[$this->model][$name])) {
			$dt = strtotime($_POST[$this->model][$name]);
			$_POST[$this->model][$name] = array("year" => date("Y", $dt), "month" => date("m", $dt), "day" => date("d", $dt));
			if (!empty($ops['time_select'])) $_POST[$this->model][$name."_time"] = array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt));
		}
		if (empty($ops['id'])) $ops['id'] = $name;
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($name));
		$select = $this->label($ops)."\n";
		//MONTH SELECT
		if (!empty($ops['default'])) dfault($_POST[$this->model][$name]['month'], date("m", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."-mm\" name=\"".$this->model."[".$name."][month]\">\n";
		foreach ($month_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$this->model][$name]['month'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//DAY SELECT
		if (!empty($ops['default'])) dfault($_POST[$this->model][$name]['day'], date("d", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."-dd\" name=\"".$this->model."[".$name."][day]\">\n";
		foreach ($day_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$this-model][$name]['day'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//YEAR SELECT
		if (!empty($ops['default'])) dfault($_POST[$this->model][$name]['month'], date("m", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."\" class=\"split-date range-low-".date("Y-m-d")." no-transparency\" name=\"".$this->model."[".$name."][year]\">\n";
		foreach ($year_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$this->model][$name]['year'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//TIME
		if (!empty($ops['time_select'])) $select .= $this->time_select(array_merge(array($name), $ops));
		return $select;
	}

	function time_select($ops) {
		//SETUP OPTION ARRAYS
		$hour_options = array("Hour" => "-1");
		for($i=1;$i<13;$i++) $hour_options[$i] = $i;
		$minutes_options = array("Minutes" => "-1", "00" => "00", "15" => "15", "30" => "30", "45" => "45");
		$ampm_options = array("AM" => "am", "PM" => "pm");
		//ID, NAME, LABEL, ERRORS
		$select = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select .= form::label($ops)."\n";
		//HOUR SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['hour'], date("H", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."-hour\" name=\"".$ops['postvar']."[".$ops['name']."][hour]\">\n";
		foreach ($hour_options as $caption => $val) $select .= "<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['hour'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//MINUTE SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['minutes'], date("i", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."-minutes\" name=\"".$ops['postvar']."[".$ops['name']."][minutes]\">\n";
		foreach ($minutes_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['minutes'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//AMPM SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['ampm'], date("a", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."\" name=\"".$ops['postvar']."[".$ops['name']."][ampm]\">\n";
		foreach ($ampm_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['ampm'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		return $select;
	}
	
	function textarea($ops) {
		$ops = starr::star($ops);
		$name = array_shift($ops);
		$ops['name'] = $name;
		//id, name, and type
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input = $this->label($ops)."\n";
		if (empty($ops['cols'])) $ops['cols'] = "90";
		if (empty($ops['rows'])) $ops['rows'] = "90";
		//POSTed or default value
		if (!empty($_POST[$this->model][$ops['name']])) $ops['content'] = $_POST[$this->model][$ops['name']];
		else if (!empty($ops['default'])) {
			$ops['content'] = $ops['default'];
			unset($ops['default']);
		}
		//name close
		$ops['name'] = $this->model."[".$ops['name']."]";
		$ops = array_merge(array("textarea"), $ops);
		return $input.$this->tag($ops);
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
