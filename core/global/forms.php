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
	if (success($global_form->model, $global_form->action)) $class = "success";
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
	global $global_form;
	echo $global_form->text($ops);
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
	global $global_form;
	echo $global_form->password($ops);
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
	global $global_form;
	echo $global_form->hidden($ops);
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
	global $global_form;
	echo $global_form->submit($ops);
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
	global $global_form;
	echo $global_form->button($label, $ops);
}
/**
 * outputs a file input
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function file_select($ops) {
	global $global_form;
	echo $global_form->file($ops);
}
/**
 * outputs a checkbox input
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function checkbox($ops) {
	global $global_form;
	echo $global_form->checkbox($ops);
}
/**
 * outputs a radio button
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function radio($ops) {
	global $global_form;
	echo $global_form->radio($ops);
}
/**
 * outputs an input
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function input($type, $ops) {
	global $global_form;
	echo $global_form->input($type, $ops);
}
/**
 * outputs a select field
 * @ingroup forms
 * @param string $ops the field info
 * @param array $options the option elements
 * @ingroup form
 */
function select($ops, $options=array()) {
	global $global_form;
	echo $global_form->select($ops, $options);
}
/**
 * outputs a date select
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function date_select($ops) {
	global $global_form;
	echo $global_form->date_select($ops);
}
/**
 * outputs a time select
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function time_select($ops) {
	global $global_form;
	echo $global_form->time_select($ops);
}
/**
 * outputs a textarea
 * @ingroup forms
 * @param string $ops the options
 * @ingroup form
 */
function textarea($ops) {
	global $global_form;
	echo $global_form->textarea($ops);
}
/**
 * outputs a closing form tag
 * @ingroup forms
 * @ingroup form
 */
function close_form() {
	global $global_form;
	render("form/close", $global_form->scope);
}
?>
