<?php
	efault($field['cols'], "35");
	efault($field['rows'], "8");
	//POSTed or default value
	$value = $this->get($field['name']);
	if (!empty($field['default'])) {
		efault($value, $field['default']);
		unset($field['default']);
	}
	$this->assign("value", $this->set($field['name'], htmlentities($value, ENT_QUOTES, "UTF-8")));
?>
