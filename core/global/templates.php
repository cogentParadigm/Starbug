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
function assign($key, $value, $scope="global") {
	global $sb;
	$sb->import("core/lib/Renderer");
	global $renderer;
	$renderer->assign($key, $value, $scope);
}
/**
	* render a template
	* @ingroup templates
	* @param string $path the path, relative to the request prefix and without the file extension
	* @param string $scope a rendering scope. if this is empty we will use the active scope. If there is no active scope we will use 'global'
	*/
function render($path, $scope="") {
	global $sb;
	$sb->import("core/lib/Renderer");
	global $renderer;
	$renderer->render($path, $scope);
}
/**
	* capture a rendered template
	* @ingroup templates
	* @param string $path the path, relative to the request prefix and without the file extension
	*/
function capture($path, $scope="global") {
	global $sb;
	$sb->import("core/lib/Renderer");
	global $renderer;
	return $renderer->capture($path, $scope);
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
	efault($layout, "two-column-right");
	if ($render) render($layout, "layouts");
	else return capture($layout, "layouts");
}
/**
	* render content
	* @ingroup templates
	* @param string $layout the layout to render
	*/
function render_content($content="", $render=true) {
	$view = request("file");
	if (empty($content) && !empty($view)) render_view($content, $render);
	else render_blocks($content);
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
?>
