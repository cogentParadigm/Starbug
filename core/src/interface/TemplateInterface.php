<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/TemplateInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * A simple interface for template rendering
 */
interface TemplateInterface {

	/**
	* assign a variable
	* @param string $key variable name
	* @param string $value variable value
	*/
	public function assign($key, $value = null);

	/**
	* output a template
	* @param string $path relative path to the template from the view directory without file extension
	*/
	public function output($paths = array(""), $params = array(), $options = array());

	/**
	* capture a rendered template
	* @param string $path relative path to the template from the view directory without file extension
	*/
	public function get($paths = array(""), $params = array(), $options = array());

	/**
	* render a child template
	* @param mixed $paths a path or an array of paths to try
	* @param array $params an array of variables to inject
	* @param array $options additional options such as the scope or prefix
	*/
	public function render($paths = array(""), $params = array(), $options = array());

	/**
	* capture a child template
	* @param mixed $paths a path or an array of paths to try
	* @param array $params an array of variables to inject
	* @param array $options additional options such as the scope or prefix
	* @return string the output of the template
	*/
	public function capture($paths = array(""), $params = array(), $options = array());

	/**
	* convenience method to render a template from the views directory
	* @copydoc render
	*/
	public function render_view($paths = array(""), $params = array());

	/**
	* convenience method to render a template from the layouts directory
	* @copydoc render
	*/
	public function render_layout($paths = array(""), $params = array());

	/**
	* render content blocks from the database for the specified region
	* @param string $region the region to render content for
	*/
	public function render_content($region = "content");

	/**
	* render all variants of {$tag}.{$topic} for each tag passed, plus 'global'
	*/
	public function publish($topic, $tags = array(), $params = array());
}
