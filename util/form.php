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
	function build($postvar, $args, $fields, $extras) {
		$args = starr::star($args);
		efault($args['url'], $_SERVER['REQUEST_URI']);
		efault($args['method'], "post");
		$open = '<form class="'.$postvar.'_form" action="'.$args['url'].'" method="'.$args['method'].'"';
		$contents = '<input class="action" name="action['.$postvar.']" type="hidden" value="'.$args['action'].'" />'."\n";
		if (!empty($_POST[$postvar]['id'])) $contents .= '<input id="id" name="'.$postvar.'[id]" type="hidden" value="'.$_POST[$postvar]['id'].'" />'."\n";
		foreach($fields as $key => $value) {
			$value = starr::star($value);
			foreach($value as $k => $v) foreach($extras as $i => $extra) if ($v == '$'.$i) $value[$k] = $extra;
			$value['postvar'] = $postvar;
			if (is_numeric($key)) $contents .= form::tag($value, $extras);
			else {
				if (!isset($value['fields'])) $value['name'] = $key;
				$func = array_shift($value);
				if ($func == "bin") $enctype = true;
				$contents .= form::$func($value, $extras);
			}
		}
		if ($enctype) $open .= ' enctype="multipart/form-data"';
		return $open.">\n".$contents."\t</form>";
	}
	
	function fields($postvar, $fields, $extras) {
		$contents = "";
		foreach($fields as $key => $value) {
			if(!is_array($value)) $value = starr::star($value);
			foreach($value as $k => $v) foreach($extras as $i => $extra) if ($v == '$'.$i) $value[$k] = $extra;
			$value['postvar'] = $postvar;
			if(is_numeric($key)) $content .= form::tag($value, $extras);
			else {
				if (!isset($value['fields'])) $value['name'] = $key;
				$func = array_shift($value);
				$content .= form::$func($value, $extras);
			}
		}
		return $content;
	}
	function fieldset($args, $extras) {
		if (isset($args['legend'])) {
			$legend = "<legend>$args[legend]</legend>";
			unset($args['legend']);
		}
		$args = array("fieldset", "fields" => $args['fields'], "postvar" => $args['postvar']);
		if (!empty($legend)) $args['content'] = $legend;
		return form::tag($args, $extras);
	}
	function label(&$ops) {
		if (!($ops['nolabel'])) $lab = '<label for="'.$ops['id'].'"'.((empty($ops['identifier_class'])) ? '' : ' class="'.$ops['identifier_class'].'"').'>'.$ops['label']."</label>";
		if (!empty($ops['error'])) foreach ($ops['error'] as $prefix => $message) if (!empty($sb->errors[$ops['postvar']][$prefix."Error"])) $lab .= "\n"."<span class=\"error\">".$message."</span>";
		unset($ops['label']);
		unset($ops['error']);
		return $lab;
	}
	function text($ops, $extra) {
		$ops['type']='text';
		return form::input($ops, $extra);
	}
	function password($ops, $extra) {
		$ops['type']='password';
		return form::input($ops, $extra);
	}
	function hidden($ops) {
		return '<input type="hidden" name="'.$ops['postvar'].'['.$ops['name'].']" value="'.$ops['value'].'" />';
	}
	function submit($ops) {
		return '<input type="submit" class="'.$ops['class'].'" value="'.$ops['name'].'" />';
	}
	function bin($ops, $extra) {
		$ops['type'] = 'file';
		return form::input($ops, $extra);
	}
	function image($ops) {
		return '<input type="image" src="'.$ops['src'].'"/>';
	}
	function checkbox($ops, $extra) {
		$ops['type'] = "checkbox";
		return form::input($ops, $extra);
	}
	function input($ops, $extras) {
		//id, label, and class
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input = form::label($ops)."\n";
		$ops['class'] = (empty($ops['class'])) ? $ops['type'] : $ops['class']." ".$ops['type'];
		//POSTed or default value
		if (!empty($_POST[$ops['postvar']][$ops['name']])) $ops['value'] = $_POST[$ops['postvar']][$ops['name']];
		else if (!empty($ops['default'])) {
			$ops['value'] = $ops['default'];
			unset($ops['default']);
		}
		//name and close
		$ops['name'] = $ops['postvar']."[".$ops['name']."]";
		$ops = array_merge(array("input"), $ops);
		return $input.form::tag($ops, $extras, true);
	}
	function custom($ops) {
		return $ops['content'];
	}
	function eip_text($ops) {
		//id, name, and type
		$input = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input .= form::label($ops)."\n";
		$input .= $ops['before'].'<span id="'.$ops['id'].'">'.$ops['prefix'].'<span class="editable"	onclick="edit_eip_text(\''.$ops['id'].'\', \''.$ops['postvar'].'['.$ops['name'].']\');return false;">'.((!empty($_POST[$ops['postvar']][$ops['name']])) ? $_POST[$ops['postvar']][$ops['name']] : $ops['default']).'</span>'.$ops['suffix'].'</span>'.$ops['after'];
		return $input;
	}

	function select($ops) {
		//id, name, and type
		$select = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select .= form::label($ops)."\n";
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']], $ops['default']);
		if (!empty($ops['range'])) {
			$range = split(":", $ops['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $ops['options'][$i] = $i;
		}
		if (!empty($ops['class'])) $class = " class=\"$ops[class]\"";
		$select .= "<select id=\"".$ops['id']."\" name=\"$ops[postvar]"."[".$ops['name']."]\"$class>\n";
		foreach ($ops['options'] as $caption => $val) $select .= "<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		return $select."</select>\n";
	}

	function date_select($ops) {
		//SETUP OPTION ARRAYS
		$year = date("Y");
		$year_options = array("Year" => "-1", $year => $year, (((int) $year)+1) => (((int) $year)+1));
		$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
		$day_options = array("Day" => "-1");
		for($i=1;$i<32;$i++) $day_options["$i"] = $i;
		//ID, NAME, LABEL, ERRORS
		if (!empty($_POST[$ops['postvar']][$ops['name']])) {
			$dt = strtotime($_POST[$ops['postvar']][$ops['name']]);
			$_POST[$ops['postvar']][$ops['name']] = array("year" => date("Y", $dt), "month" => date("m", $dt), "day" => date("d", $dt));
			if (!empty($ops['time_select'])) $_POST[$ops['postvar']][$ops['name']."_time"] = array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt));
		}
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select = form::label($ops)."\n";
		//MONTH SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['month'], date("m", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."-mm\" name=\"".$ops['postvar']."[".$ops['name']."][month]\">\n";
		foreach ($month_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['month'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//DAY SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['day'], date("d", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."-dd\" name=\"".$ops['postvar']."[".$ops['name']."][day]\">\n";
		foreach ($day_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['day'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//YEAR SELECT
		if (!empty($ops['default'])) dfault($_POST[$ops['postvar']][$ops['name']]['month'], date("m", strtotime($ops['default'])));
		$select .= "<select id=\"".$ops['id']."\" class=\"split-date range-low-<?php echo date(\"Y-m-d\"); ?> no-transparency\" name=\"".$ops['postvar']."[".$ops['name']."][year]\">\n";
		foreach ($year_options as $caption => $val) $select .= "\t<option value=\"$val\"".(($_POST[$ops['postvar']][$ops['name']]['year'] == $val) ? " selected=\"true\"" : "").">$caption</option>\n";
		$select .= "</select>\n";
		//TIME
		if (!empty($ops['time_select'])) $select .= form::time_select($ops);
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
		//id, name, and type
		$input = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$input .= form::label($ops)."\n";
		$sizestring = "cols=\"".((empty($ops['cols'])) ? "90" : $ops['cols'])."\" rows=\"".((empty($ops['rows'])) ? "10" : $ops['rows'])."\"";
		$input .= '<textarea id="'.$ops['id'].'" name="'.$ops['postvar']."[".$ops['name'].']" '.$sizestring.'>';
		//POSTed or default value
		if (!empty($_POST[$ops['postvar']][$ops['name']])) $input .= $_POST[$ops['postvar']][$ops['name']];
		else if (!empty($ops['default'])) $input .= $ops['default'];
		//close
		return $input."</textarea>\n";
	}

	function tag($tag, $extras, $self=false) {
		if (is_array($tag)) $name = array_shift($tag);
		else {
			$parts = split("\t", $tag, 1);
			$name = $parts[0];
			if (count($parts) > 1) $tag = starr::star($parts[1]);
		}
		$content = $tag['content']; unset($tag['content']); $str = "";
		if (!empty($tag['fields'])) {
			$content .= form::fields($tag['postvar'], $tag['fields'], $extras);
			unset($tag['fields']);
		}
		unset($tag['postvar']);
		foreach($tag as $key => $value) $str .= " $key=\"$value\"";
		return ($self) ? "<$name$str />" : "<$name$str>$content</$name>";
	}
	
}
