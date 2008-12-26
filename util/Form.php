<?php
class Form {

	function render($contents, $postvar, $meth="post", $act="") {
		if(empty($act)) $act = '<?php echo htmlentities($_SERVER['."'REQUEST_URI']); ?>";
		$form = '<form class="'.$postvar.'_form" action="'.$act.'" method="'.$meth.'">'."\n";
		$form .= "\t<input class=\"action\" name=\"action[".ucwords($postvar)."]\" value=\"<?php echo \$action; ?>\" />\n";
		foreach($contents as $key => $value) {
			$value['name'] = $key;
			$value['postvar'] = $postvar;
			$form .= Form::$value['type']($value);
		}
		return $form.'</form>';
	}

	function fieldset($args) {
		$ops = $args['options'];
		unset($args['options']);
		unset($args['name']);
		unset($args['type']);
		$fs = '<fieldset>';
		foreach($args as $key => $value) {
			$value['name'] = $key;
			$fs .= Form::$value['type']($value);
		}
		return $fs.'</fieldset>';
	}

	function field($args) {
		if (isset($args['options'])) $ops = $args['options'];
		unset($args['options']);
		unset($args['type']);
		$field = "\t".'<div class="field">'."\n";
		foreach ($args as $key => $value) {
			$value['name'] = $key;
			$value['fielded'] = true;
			$field .= Form::$value['type']($value);
		}
		return $field."\t".'</div>'."\n";
	}

	function label($ops) {
		$lab = '<label for="'.$ops['id'].'"'.((empty($ops['identifier_class'])) ? '' : ' class="'.$ops['identifier_class'].'"').'>'.$ops['label']."</label>";
		$errors = array(
			"must" => "You must enter a %fieldname%.",
			"please" => "Please enter a %fieldname%."
		);
		if (!empty($ops['error'])) foreach ($ops['error'] as $prefix => $message) $lab .= "\n".$ops['tabs']."<?php if (!empty(\$this->errors['".$ops['postvar']."']['".$prefix."Error'])) { ?><span class=\"error\">".(!empty($errors[$message])?str_replace("%fieldname%", $ops['name'], $errors[$message]):$message)."</span><?php } ?>";
		return $lab;
	}

	function text($ops) {
		$ops['input_type']='text';
		return Form::input($ops);
	}

	function password($ops) {
		$ops['input_type']='password';
		return Form::input($ops);
	}

	function hidden($ops) {
		$ops['input_type']='hidden';
		return Form::input($ops);
	}

	function submit($ops) {
		$ops['input_type']='submit';
		$ops['default']=$ops['value'];
		return Form::input($ops);
	}

	function image($ops) {
		return '<input type="image" src="'.$ops['src'].'"/>';
	}

	function input($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return Form::field(array($ops['name'] => $ops));
		$ops['tabs'] = $tabs;
		//id, name, and type
		$input = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		if (!isset($ops['default'])) if (!isset($ops['error'][$ops['name']])) $ops['error'][$ops['name']] = "please";
		$input .= $tabs.Form::label($ops)."\n";
		$input .= $tabs.'<input id="'.$ops['id'].'" name="'.$ops['postvar']."[".$ops['name'].']" type="'.$ops['input_type'].'"';
		//POSTed or default value
		$input .= "<?php if (!empty(\$_POST['".$ops['postvar']."']['".$ops['name']."'])) { ?> value=\"<?php echo \$_POST['".$ops['postvar']."']['".$ops['name']."']; ?>\"<?php } ";
		if (!empty($ops['default'])) {
			$input .= "else { ?> value=\"".$ops['default']."\"<?php } ?>";
			if (!empty($ops['onfocus'])) $input .= '" onfocus="if(this.value=="'.$ops['default'].'"){this.value="";}else{this.select();this.focus();}"';
		} else $input .= "?>";
		//size
		if (!empty($ops['size'])) $input .= ' size="'.$ops['size'].'"';
		//close
		return $input." />\n";
	}

	function select($ops) {
		if (!empty($ops['nofield'])) $tabs = "\t";
		else if (!empty($ops['fielded'])) $tabs = "\t\t";
		else return Form::field(array($ops['name'] => $ops));
		$ops['tabs'] = $tabs;
		//id, name, and type
		$select = "";
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (empty($ops['label'])) $ops['label'] = str_replace("_", " ", ucwords($ops['name']));
		$select .= $tabs.Form::label($ops)."\n";
		if (!empty($ops['default'])) $select .= $tabs."<?php dfault(\$_POST['".$ops['postvar']."']['".$ops['name']."'], \"".$ops['default']."\"); ?>\n";
		if (!empty($ops['range'])) {
			$range = split(":", $ops['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $ops['options'][$i] = $i;
		}
		$select .= $tabs."<select id=\"".$ops['id']."\" name=\"".$ops['postvar']."[".$ops['name']."]\">\n";
		foreach ($ops['options'] as $caption => $val) $select .= $tabs."\t<option value=\"$val\"<?php if (\$_POST['".$ops['postvar']."']['".$ops['name']."'] == \"$val\") { ?> selected=\"true\"<?php } ?>>$caption</option>\n";
		return $select.$tabs."</select>\n";
	}
}