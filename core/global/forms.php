<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/forms.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup forms
 */
/**
 * @defgroup forms
 * global functions
 * @ingroup global
 */
/**
 * alias of render_form in core/global/templates
 */
function form($arg, $render=true) {
	render_form($arg, $render);
}
/**
 * retrieve a global form instance
 * @ingroup forms
 */
function global_form($set="") {
	global $global_form;
	if (empty($set)) {
		if (empty($global_form)) $global_form = new form();
	} else $global_form = $set;
	return $global_form;
}
/**
 * creates a new form and outputs the opening form tag and some hidden inputs
 * @ingroup forms
 * @param string $options the options for the form
 * @param string $atts attributes for the form tag
 */
function open_form($options, $atts="") {
	global $sb;
	$sb->import("util/form");
	global $global_form;
	$global_form = new form($options);
	$open = "";
	$atts = star($atts);
	if (success($global_form->model, $global_form->action)) $class = "submitted";
	else if (failure($global_form->model, $global_form->action)) $class = "errors";
	else $class = "clean";
	$atts['class'] = (empty($atts['class'])) ? $class : $atts['class']." ".$class;
	foreach($atts as $k => $v) $open .= $k.'="'.$v.'" ';
	$global_form->open(rtrim($open, " "));
}
function f($control, $field, $options=array()) {
	$form = global_form();
	echo $form->form_control($control, $field, $options);
}
/**
 * generates a text field
 * @ingroup forms
 * @param star $ops an option string starting with the input name, and including HTML attributes
 *									[name]: the input name. If there is a model associated with this form, the name is relative, eg. 'group[]' might become 'users[group][]'
 *									label: The label displayed above the input. The default label is the name option replacing underscores with spaces and passed to ucwords.
 * 									nolabel: if this is set, no label will be displayed
 * 									default: a default value to use if $_POST[$model][$field_name] is not set
 *
 * 									for the first paramater, you can leave out the key. For example, here  'name:' is left out:
 * 									text("title  label:The Title  default:Untitled");
 */
function text($ops) {
	$form = global_form();
	echo $form->text($ops);
}
/**
 * generates a password field
 * @ingroup forms
 * @param star $ops an option string starting with the input name, and including HTML attributes
 *									[name]: the input name. If there is a model associated with this form, the name is relative, eg. 'group[]' might become 'users[group][]'
 *									label: The label displayed above the input. The default label is the name option replacing underscores with spaces and passed to ucwords.
 * 									nolabel: if this is set, no label will be displayed
 *
 * 									for the first paramater, you can leave out the key. For example, here  'name:' is left out:
 * 									password("password  label:Your Password");
 */
function password($ops) {
	$form = global_form();
	echo $form->password($ops);
}
/**
 * generates a hidden input field
 * @ingroup forms
 * @param star $ops an option string starting with the input name, and including HTML attributes
 *									[name]: the input name. If there is a model associated with this form, the name is relative, eg. 'group[]' might become 'users[group][]'
 * 									default: a default value to use if $_POST[$model][$field_name] is not set
 *
 * 									for the first paramater, you can leave out the key. For example, here  'name:' is left out:
 * 									hidden("article_id  default:1");
 */
function hidden($ops) {
	$form = global_form();
	echo $form->hidden($ops);
}
/**
 * generates a submit input
 * @ingroup forms
 * @param star $ops an option string of HTML attributes
 */
function submit($ops="") {
	$form = global_form();
	echo $form->submit($ops);
}
/**
 * generates a submit button
 * @ingroup forms
 * @param string $label the inner HTML of the button
 * @param star $ops an option string of HTML attributes
 */
function button($label, $ops="") {
	$ops = star($ops);
	efault($ops['type'], "submit");
	$ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ")."btn";
	assign("label", $label);
	assign("attributes", $ops);
	render("form/button");
}
	/**
	 * generates a file input
	 * @ingroup forms
	 * @param star $ops an option string starting with the input name, and including HTML attributes
	 *									[name]: the input name
	 *									label: The label displayed above the input. The default label is the name option replacing underscores with spaces and passed to ucwords.
	 * 									nolabel: if this is set, no label will be displayed
	 * 									default: a default value to use
	 */
function file_select($ops) {
	$form = global_form();
	echo $form->file($ops);
}
	/**
	 * generates a checkbox
	 * @ingroup forms
	 * @param star $ops an option string starting with the input name, and including HTML attributes
	 *									[name]: the input name. If there is a model associated with this form, the name is relative, eg. 'group[]' might become 'users[group][]'
	 * 									value: you must specify a value. The checkbox will be checked if the POST contains this value.
	 *									label: The label displayed above the input. The default label is the name option replacing underscores with spaces and passed to ucwords.
	 * 									nolabel: if this is set, no label will be displayed
	 *
	 * 									for the first paramater, you can leave out the key. For example, here  'name:' is left out:
	 * 									checkbox("is_active  value:1");
	 */
function checkbox($ops) {
	$form = global_form();
	echo $form->checkbox($ops);
}
/**
 * outputs a radio button
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function radio($ops) {
	$form = global_form();
	echo $form->radio($ops);
}
/**
 * outputs an input
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function input($type, $ops) {
	$form = global_form();
	echo $form->input($type, $ops);
}
/**
 * outputs a select field
 * @ingroup forms
 * @param string $ops the field info
 * @param array $options the option elements
 * @ingroup form
 */
function select($ops, $options=array()) {
	$form = global_form();
	echo $form->select($ops, $options);
}
/**
 * outputs a category select
 * @ingroup forms
 * @param string $ops the options
 */
function category_select($ops) {
	$form = global_form();
	echo $form->category_select($ops);
}
/**
 * outputs a multiple category select - functionally equivalent to multiple select but using checkboxes and labels
 * @ingroup forms
 * @param string $ops the options
 */
function multiple_category_select($ops) {
	$form = global_form();
	echo $form->multiple_category_select($ops);
}
/**
 * outputs a date select
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function date_select($ops) {
	$form = global_form();
	echo $form->date_select($ops);
}
/**
 * outputs a time select
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function time_select($ops) {
	$form = global_form();
	echo $form->time_select($ops);
}
/**
 * outputs a textarea
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function textarea($ops) {
	$form = global_form();
	echo $form->textarea($ops);
}
/**
 * outputs a closing form tag
 * @ingroup forms
 * @ingroup form
 */
function close_form() {
	$form = global_form();
	render("form/close", $form->scope);
}
?>
