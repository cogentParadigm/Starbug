<?php
# Copyright (C) 2008-2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/FormCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class FormCollection extends Collection {
	public $copying = false;
	public function build($query, &$ops) {
		$model = $this->models->get($this->model);
		if (empty($ops['action'])) $ops['action'] = "create";
		$query->action($ops['action'], $query->model);
		$query->condition($query->model.".id", $ops['id']);
		$fields = $model->hooks;
		if (!empty($model->base)) {
			unset($fields["id"]);
			foreach($model->chain($model->base) as $b) unset($fields[$b."_id"]);
		}
		foreach ($fields as $fieldname => $field) {
			if ($this->models->has($field['type'])) {
				if (empty($field['column'])) $field['column'] = "id";
				$query->select($query->model.'.'.$fieldname.'.'.$field['column'].' as '.$fieldname);
			}
		}
		$parent = $model->base;
		while (!empty($parent)) {
			foreach ($this->models->get($parent)->hooks as $column => $field) {
				if ($this->models->has($field['type'])) {
					if (empty($field['column'])) $field['column'] = "id";
					$query->select($query->model.'.'.$column.'.'.$field['column'].' as '.$column);
				}
			}
			$parent = $this->models->get($parent)->base;
		}
		return $query;
	}
	public function filterQuery($query, &$ops) {
		if (!empty($ops['copy'])) {
			$this->copying = true;
		}
		$query = parent::filterQuery($query, $ops);
		return $query;
	}
}
?>
