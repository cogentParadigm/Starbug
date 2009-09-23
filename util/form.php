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

	function render($postvar, $meth, $action, $submit_to, $fields) {
		if (empty($submit_to)) $submit_to = $_SERVER['REQUEST_URI'];
		$open = "<form class=\"".$postvar."_form\" action=\"$submit_to\" method=\"$meth\"";
		$contents = "\t<input class=\"action\" name=\"action[$postvar]\" type=\"hidden\" value=\"$action\" />\n";
		if (!empty($_POST[$postvar]['id'])) $contents .= "\t<input id=\"id\" name=\"".$postvar."[id]\" type=\"hidden\" value=\"".$_POST[$postvar]['id']."\" />\n";
		foreach($fields as $key => $value) {
			$value['name'] = $key;
			$value['postvar'] = $postvar;
			if ($value['type'] == "bin") $enctype = true;
			$contents .= form::$value['type']($value);
		}
		if ($enctype) $open .= " enctype=\"multipart/form-data\"";
		return $open.">\n".$contents."\t</form>";
	}

	function fieldset($args) {
		$ops = $args['options'];
		unset($args['options']);
		unset($args['name']);
		unset($args['type']);
		$fs = '<fieldset>';
		foreach($args as $key => $value) {
			$value['name'] = $key;
			$fs .= form::$value['type']($value);
		}
		return $fs.'</fieldset>';
	}
	
	function field($args) {
		$ops = $args['options'];
		unset($args['options']);
		$field = "\t".'<'.(isset($ops['type'])?$ops['type']:"div").' class="field">'."\n";
		foreach ($args as $key => $value) {
			$value['name'] = $key;
			$value['fielded'] = true;
			$field .= form::$value['type']($value);
		}
		return $field."\t".'</'.(isset($ops['type'])?$ops['type']:"div").'>'."\n";
	}

	function label($ops) {
		$lab = '<label for="'.$ops['id'].'"'.((empty($ops['identifier_class'])) ? '' : ' class="'.$ops['identifier_class'].'"').'>'.$ops['label']."</label>";
		if (!empty($ops['error'])) foreach ($ops['error'] as $prefix => $message) if (!empty($sb->errors[$ops['postvar']][$prefix."Error"])) $lab .= "\n".$ops['tabs']."<span class=\"error\">".$message."</span>";
		return $lab;
	}

	function text($ops) {
		$ops['input_type']='text';
		return form::input($ops);
	}

	function password($ops) {
		$ops['input_type']='password';
		return form::input($ops);
	}

	function hidden($ops) {
		$ops['input_type']='hidden';
		return form::input($ops);
	}

	function submit($ops) {
		return '<input type="submit" class="'.$ops['class'].'" value="'.$ops['name'].'" />';
	}

	function bin($ops) {
		$ops['input_type'] = 'file';
		return form::input($ops);
	}

	function image($ops) {
		return '<input type="image" src="'.$ops['src'].'"/>';
	}
	
	function input($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return form::field(array($ops['name'] => $ops, "options" => (isset($ops['field'])?$ops['field']:array())));
		$ops['tabs'] = $tabs;
		//id, name, and type
		$input = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input .= $tabs.form::label($ops)."\n";
		$input .= $tabs.'<input id="'.$ops['id'].'" name="'.$ops['postvar']."[".$ops['name'].']" type="'.$ops['input_type'].'" class="'.$ops['input_type'].'"';
		//POSTed or default value
		if (!empty($_POST[$ops['postvar']][$ops['name']])) $input .= " value=\"".$_POST[$ops['postvar']][$ops['name']]."\"";
		else if (!empty($ops['default'])) {
			$input .= " value=\"".$ops['default']."\"";
			if (!empty($ops['onfocus'])) $input .= ' onfocus="if(this.value=="'.$ops['default'].'"){this.value="";}else{this.select();this.focus();}"';
		}
		//size
		if (!empty($ops['size'])) $input .= ' size="'.$ops['size'].'"';
		//close
		return $input." />\n";
	}
	
	function checkbox($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return form::field(array($ops['name'] => $ops, "options" => (isset($ops['field'])?$ops['field']:array())));
		$ops['tabs'] = $tabs;
		$ops['input_type'] = "checkbox";
		//id, name, and type
		$input = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input .= $tabs.'<input id="'.$ops['id'].'" name="'.$ops['postvar']."[".$ops['name'].']" type="'.$ops['input_type'].'"';
		//POSTed or default value
		if (isset($_POST[$ops['postvar']][$ops['name']])) $input .= " value=\"".$_POST[$ops['postvar']][$ops['name']]."\"";
		else if (!empty($ops['default'])) $input .= " value=\"".$ops['default']."\"";
		//size
		if (!empty($ops['size'])) $input .= ' size="'.$ops['size'].'"';
		//close
		$input .= " />\n";
		$input .= $tabs.form::label($ops)."\n";
		return $input;
	}

	function select($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return form::field(array($ops['name'] => $ops, "options" => (isset($ops['field'])?$ops['field']:array())));
		$ops['tabs'] = $tabs;
		//id, name, and type
		$select = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select .= $tabs.form::label($ops)."\n";
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']], $ops['default']);
		if (!empty($ops['range'])) {
			$range = split(":", $ops['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $ops['options'][$i] = $i;
		}
		$select .= $tabs."<select id=\"".$ops['id']."\" name=\"".$ops['postvar']."[".$ops['name']."]\">\n";
		foreach ($ops['options'] as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		return $select.$tabs."</select>\n";
	}

	function date_select($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return form::field(array($ops['name'] => $ops, "options" => (isset($ops['field'])?$ops['field']:array())));
		//SETUP OPTION ARRAYS
		$year = date("Y");
		$year_options = array("Year" => "-1", $year => $year, (((int) $year)+1) => (((int) $year)+1));
		$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
		$day_options = array("Day" => "-1");
		for($i=1;$i<32;$i++) $day_options["$i"] = $i;
		//ID, NAME, LABEL, ERRORS
		$ops['tabs'] = $tabs;
		if (!empty($_POST[$ops['postvar']][$ops['name']])) {
			$dt = strtotime($_POST[$ops['postvar']][$ops['name']]);
			$_POST[$ops['postvar']][$ops['name']] = array("year" => date("Y", $dt), "month" => date("m", $dt), "day" => date("d", $dt));
			if (!empty($ops['time_select'])) $_POST[$ops['postvar']][$ops['name']."_time"] = array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt));
		}
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select = $tabs.form::label($ops)."\n";
		//MONTH SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['month'], date("m", strtotime($ops['default'])));
		$select .= $tabs."<select id=\"".$ops['id']."-mm\" name=\"".$ops['postvar']."[".$ops['name']."][month]\">\n";
		foreach ($month_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['month'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= $tabs."</select>\n";
		//DAY SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['day'], date("d", strtotime($ops['default'])));
		$select .= $tabs."<select id=\"".$ops['id']."-dd\" name=\"".$ops['postvar']."[".$ops['name']."][day]\">\n";
		foreach ($day_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['day'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= $tabs."</select>\n";
		//YEAR SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['month'], date("m", strtotime($ops['default'])));
		$select .= $tabs."<select id=\"".$ops['id']."\" class=\"split-date range-low-<?php echo date(\"Y-m-d\"); ?> no-transparency\" name=\"".$ops['postvar']."[".$ops['name']."][year]\">\n";
		foreach ($year_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['year'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= $tabs."</select>\n";
		//TIME
		if (!empty($ops['time_select'])) $select .= form::time_select($ops);
		return $select;
	}

	function time_select($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return form::field(array($ops['name'] => $ops, "options" => (isset($ops['field'])?$ops['field']:array())));
		//SETUP OPTION ARRAYS
		$hour_options = array("Hour" => "-1");
		for($i=1;$i<13;$i++) $hour_options[$i] = $i;
		$minutes_options = array("Minutes" => "-1", "00" => "00", "15" => "15", "30" => "30", "45" => "45");
		$ampm_options = array("AM" => "am", "PM" => "pm");
		//ID, NAME, LABEL, ERRORS
		$ops['tabs'] = $tabs;
		$select = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select .= $tabs.form::label($ops)."\n";
		//HOUR SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['hour'], date("H", strtotime($ops['default'])));
		$select .= $tabs."<select id=\"".$ops['id']."-hour\" name=\"".$ops['postvar']."[".$ops['name']."][hour]\">\n";
		foreach ($hour_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['hour'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= $tabs."</select>\n";
		//MINUTE SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['minutes'], date("i", strtotime($ops['default'])));
		$select .= $tabs."<select id=\"".$ops['id']."-minutes\" name=\"".$ops['postvar']."[".$ops['name']."][minutes]\">\n";
		foreach ($minutes_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['minutes'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= $tabs."</select>\n";
		//AMPM SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['ampm'], date("a", strtotime($ops['default'])));
		$select .= $tabs."<select id=\"".$ops['id']."\" name=\"".$ops['postvar']."[".$ops['name']."][ampm]\">\n";
		foreach ($ampm_options as $caption => $val) $select .= $tabs."\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['ampm'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= $tabs."</select>\n";
		return $select;
	}
	
	function textarea($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return form::field(array($ops['name'] => $ops, "options" => (isset($ops['field'])?$ops['field']:array())));
		$ops['tabs'] = $tabs;
		//id, name, and type
		$input = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input .= $tabs.form::label($ops)."\n";
		$sizestring = "cols=\"".((empty($ops['cols'])) ? "90" : $ops['cols'])."\" rows=\"".((empty($ops['rows'])) ? "10" : $ops['rows'])."\"";
		$input .= $tabs.'<textarea id="'.$ops['id'].'" name="'.$ops['postvar']."[".$ops['name'].']" '.$sizestring.'>';
		//POSTed or default value
		if (!empty($_POST[$ops['postvar']][$ops['name']])) $input .= $_POST[$ops['postvar']][$ops['name']];
		else if (!empty($ops['default'])) $input .= $ops['default'];
		//close
		return $input."</textarea>\n";
	}

}
