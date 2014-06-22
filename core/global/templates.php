<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/templates.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup templates
 */
/**
 * @defgroup templates
 * global functions
 * @ingroup global
 */
/**
	* assign vars to the global renderer
	* @ingroup templates
	* @param string $key the variable name
	* @param string $value the value to assign
	*/
function assign($key, $value) {
	global $sb;
	$sb->import("core/lib/Renderer");
	global $renderer;
	$renderer->assign($key, $value);
}
/**
	* render a template
	* @ingroup templates
	* @param string $path the path, relative to the request prefix and without the file extension
	* @param string $scope a rendering scope. if this is empty we will use the active scope. If there is no active scope we will use 'global'
	*/
function render($path, $scope="", $prefix="") {
	global $sb;
	$sb->import("core/lib/Renderer");
	global $renderer;
	$renderer->render($path, $scope, $prefix);
}
/**
	* capture a rendered template
	* @ingroup templates
	* @param string $path the path, relative to the request prefix and without the file extension
	*/
function capture($path, $scope="", $prefix="") {
	global $sb;
	$sb->import("core/lib/Renderer");
	global $renderer;
	return $renderer->capture($path, $scope, $prefix);
}
/**
	* render a region
	* @ingroup templates
	* @param string $region the region to render
	*/
function render_region($region) {
	if (is_array($region)) foreach($region as $key => $value) render_region($key);
	else {
		assign("region", $region);
		render("region");
	}
}
/**
	* render a view
	* @ingroup templates
	* @param string $view the view to render
	*/
function render_view($view="", $render=true) {
	if (empty($view)) $view = empty(request()->file) ? locate_view(request()->uri) : request()->file;
	if ($render) render($view, "views");
	else return capture($view, "views");
}
/**
	* render a form
	* @ingroup templates
	* @param string $form the form to render
	*/
function render_form($form="", $render=true) {
	if ($render) render($form, "forms");
	else return capture($form, "forms");
}
/**
	* render a layout
	* @ingroup templates
	* @param string $layout the layout to render
	*/
function render_layout($layout="", $render=true) {
	efault($layout, request("layout"));
	if ($render) render($layout, "layouts");
	else return capture($layout, "layouts");
}
/**
	* render content
	* @ingroup templates
	* @param string $layout the layout to render
	*/
function render_content($content="") {
	render_blocks($content);
}
/**
	* render blocks
	* @ingroup templates
	* @param string $region render all blocks in a region
	*/
function render_blocks($region="") {
	efault($region, "content");
	assign("region", $region);
	render("blocks");
}
/**
	* render an image
	* @ingroup templates
	* @param star $src the image path plus attributes. eg. 'image("giraffe.png  class:left")'
	*/
function image($src="", $flags="i") {
	$ops = star($src);
	$src = array_shift($ops);
	$ops['src'] = uri($src, $flags);
	assign("attributes", $ops);
	render("image");
}
/**
	* render a link
	* @ingroup templates
	* @param string $text the innerHTML of the link
	* @param string $url (optional) the relative url to link to
	* @param star $attributes HTML attributes for the link
	*/
function link_to($text, $url="", $attributes=array()) {
	$attributes = star($attributes);
	if (is_array($url)) $attributes = $url;
	else if (!empty($url)) $attributes['href'] = uri($url);
	assign("attributes", $attributes);
	assign("tag", "a");
	assign("innerHTML", $text);
	render("tag");
}
/**
 * render a field
 * @ingroup templates
 * @param string $model the name of the model that the field belongs to
 * @param array $row the row that this field should be rendered from
 * @param string $field the name of the field to render
 * @param array $options formatting options
 */
function render_field($model, $row, $field, $options=array()) {
		static $hooks = array();
		if (isset(db::model($model)->hooks[$field])) {
			foreach (db::model($model)->hooks[$field] as $hook => $argument) {
				if (!isset($hooks[$hook])) $hooks[$hook] = build_hook("display/".$hook, "lib/RenderHook", "core");
				$hook = $hooks[$hook];
				$options = $hook->render($model, $row, $field, $options);
			}
		}
		if (empty($options['formatter'])) $options['formatter'] = sb($model)->hooks[$field]["type"];
		if (empty($options['label'])) $column['label'] = (!empty(sb($model)->hooks[$field]["label"])) ? sb($model)->hooks[$field]["label"] : format_label($field);
		assign("model", $model);
		assign("row", $row);
		assign("field", $field);
		assign("options", $options);
		render("field/field");
}
/**
 * build a display
 * @ingroup templates
 * @param string $type the display type (list, table, grid, csv, etc..)
 * @param array $model the model to get results from
 * @param string $name the display/query name (admin, list, select, etc..).
 * 										 For example, if you specify 'admin', then the the following model functions will be used:
 * 										 query provider: query_admin
 * 										 display provider: display_admin
 * @param array $options parameters that will be passed to the display and query functions
 */
function build_display($type, $model, $name, $options=array()) {
 	$class = get_module_class("displays/".ucwords($type)."Display", "lib/Display", "core");
 	$display = new $class($model, $name, $options);
	return $display;
}
/**
 * build and render a display
 * @ingroup templates
 * @param string $type the display type (list, table, grid, csv, etc..)
 * @param array $model the model to get results from
 * @param string $name the display/query name (admin, list, select, etc..).
 * 										 For example, if you specify 'admin', then the the following model functions will be used:
 * 										 query provider: query_admin
 * 										 display provider: display_admin
 * @param array $options parameters that will be passed to the display and query functions
 */
function render_display($type, $model, $name, $options=array()) {
	$display = build_display($type, $model, $name, $options);
	$display->render();
}
/**
 * build and capture a display
 * @ingroup templates
 * @param string $type the display type (list, table, grid, csv, etc..)
 * @param array $model the model to get results from
 * @param string $name the display/query name (admin, list, select, etc..).
 * 										 For example, if you specify 'admin', then the the following model functions will be used:
 * 										 query provider: query_admin
 * 										 display provider: display_admin
 * @param array $options parameters that will be passed to the display and query functions
 */
function capture_display($type, $model, $name, $options=array()) {
	$display = build_display($type, $model, $name, $options);
	return $display->capture();
}
?>
