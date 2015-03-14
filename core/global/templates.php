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
	(new Template("tag", array("tag" => "a", "attributes" => $attributes, "innerHTML" => $text)))->output();
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

function put($parent, $selector="", $content="") {
	if (!($parent instanceof Renderable)) {
		$content = $selector;
		$selector = $parent;
		$parent = null;
	}

	$selector = Renderable::parse_selector($selector);
	if (empty($selector['tag'])) {
		$node = $parent;
		$node->attributes = array_merge($node->attributes, $selector['attributes']);
	} else {
		$node = new Renderable($selector);
		if ($parent) $parent->appendChild($node);
	}

	$node->setText($content);

	return $node;
}
?>
