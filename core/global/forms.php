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
	$atts = starr::star($atts);
	if (success($global_form->model, $global_form->action)) $class = "submitted";
	else if (failure($global_form->model, $global_form->action)) $class = "errors";
	else $class = "clean";
	$atts['class'] = (empty($atts['class'])) ? $class : $atts['class']." ".$class;
	foreach($atts as $k => $v) $open .= $k.'="'.$v.'" ';
	$global_form->open(rtrim($open, " "));
}
/**
 * outputs a text field
 * @ingroup forms
 * @param star $ops
 * an option string of the form 'field_name  option1:value  option2:value  optionN:value' where possible options include:
 * label: label text
 * default: a default value to use if $_POST[$model][$field_name] is not set
 * any remaining options will be converted to an HTML attribute string and attached
 */
function text($ops) {
	$form = global_form();
	echo $form->text($ops);
}
/**
 * outputs a password
 * @ingroup forms
 * @param star $ops
 * an option string of the form 'field_name  option1:value  option2:value  optionN:value' where possible options include:
 * label: label text
 * default: a default value to use if $_POST[$model][$field_name] is not set
 * any remaining options will be converted to an HTML attribute string and attached
 */
function password($ops) {
	$form = global_form();
	echo $form->password($ops);
}
/**
 * outputs a hidden field
 * @ingroup forms
 * @param star $ops
 * an option string of the form 'field_name  option1:value  option2:value  optionN:value' where possible options include:
 * label: label text
 * default: a default value to use if $_POST[$model][$field_name] is not set
 * any remaining options will be converted to an HTML attribute string and attached
 */
function hidden($ops) {
	$form = global_form();
	echo $form->hidden($ops);
}
/**
 * outputs a submit button
 * @ingroup forms
 * @param star $ops
 * an option string of the form 'field_name  option1:value  option2:value  optionN:value' where possible options include:
 * content: the text inside the button
 * any remaining options will be converted to an HTML attribute string and attached
 */
function submit($ops="") {
	$form = global_form();
	echo $form->submit($ops);
}
/**
 * outputs a button
 * @ingroup forms
 * @param star $ops
 * an option string of the form 'field_name  option1:value  option2:value  optionN:value' where possible options include:
 * content: the text inside the button
 * any remaining options will be converted to an HTML attribute string and attached
 */
function button($label, $ops="") {
	$form = global_form();
	echo $form->button($label, $ops);
}
/**
 * outputs a file input
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function file_select($ops) {
	$form = global_form();
	echo $form->file($ops);
}
/**
 * outputs a checkbox input
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
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
 * outputs a select field
 * @ingroup forms
 * @param string $ops the field info
 * @param array $options the option elements
 * @ingroup form
 */
function multiple_select($ops, $options=array()) {
	$form = global_form();
	echo $form->multiple_select($ops, $options);
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
