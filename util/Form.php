<?php
class Form {

	function render($contents="", $meth="post", $act="") {
		if(empty($act)) $act = '<?php echo $_SERVER['."'REQUEST_URI']; ?>";
		$form = '<form action="'.htmlentities($act).'" method="'.$meth.'">';
		foreach($contents as $key => $value) {
			$value['name'] = $key;
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
		$ops = $args['options'];
		unset($args['options']);
		unset($args['name']);
		unset($args['type']);
		$field = '<div class="field">';
		foreach ($args as $key => $value) {
			$value['name'] = $key;
			$field .= Form::$value['type']($value);
		}
		return $field.'</div>';
	}

	function label($ops) {
		$lab = '<label for="'.$ops['id'].'"'.((empty($ops['identifier_class'])) ? '' : ' class="'.$ops['identifier_class'].'"').'>';
		if (!empty($ops['error'])) $lab .= '<span class="'.$ops['error_class'].'">'.$ops['error'].'</span>';
		return $lab.$ops['label'].'</label>';
	}

	function text_input($ops) {
		$ops['input_type']='text';
		return Form::input($ops);
	}

	function password_input($ops) {
		$ops['input_type']='password';
		return Form::input($ops);
	}

	function hidden($ops) {
		$ops['input_type']='hidden';
		return Form::input($ops);
	}

	function submit_button($ops) {
		$ops['input_type']='submit';
		$ops['default']=$ops['value'];
		return Form::input($ops);
	}

	function image_button($ops) {
		return '<input type="image" src="'.$ops['src'].'"/>';
	}

	function input($ops) {
		if (!empty($ops['field'])) {
			$field = $ops['field'];
			unset($ops['field']);
			return array('name' => $field, $ops['name'] => $ops);
		}
		$input = "";
		//id, name, and type
		if (empty($ops['id'])) $ops['id'] = $ops['name'];
		if (!empty($ops['label'])) $input .= Form::label($ops);
		$input .= '<input id="'.$ops['id'].'" name="'.$ops['name'].'" type="'.$ops['input_type'].'"';
		//POSTed or default value
		if (!empty($_POST[$ops['name']])) $input .= ' value="'.$_POST[$ops['name']].'"';
		else if (!empty($ops['default'])) {
			$input .= ' value="'.$ops['default'].'"';
			if (!empty($ops['onfocus'])) $input .= '" onfocus="if(this.value=="'.$ops['default'].'"){this.value="";}else{this.select();this.focus();}"';
		}
		//size
		if (!empty($ops['size'])) $input .= ' size="'.$ops['size'].'"';
		//close
		return $input."/>";
	}
}