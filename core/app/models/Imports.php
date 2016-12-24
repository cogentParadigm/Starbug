<?php
namespace Starbug\Core;
class Imports extends ImportsModel {

	function create($import) {
		$this->store($import);
	}

	function run($import) {
		$index = $created = $updated = 0;
		$errors = array();
		$import = $this->db->query("imports")->condition("id", $import['id'])->one();
		if (empty($import['action'])) $import['action'] = "create";
		$file = $this->db->query("files")->condition("id", $import['source'])->one();
		$fields = $this->db->query("imports_fields")->condition("imports_id", $import['id'])->sort("position")->all();
		$keys = $head = array();
		foreach ($fields as $field) {
			if ($field['update_key']) $keys[] = $field['destination'];
		}
		if (false !== ($handle = fopen("app/public/uploads/".$file['id']."_".$file['filename'], "r"))) {
			$row = fgetcsv($handle);
			foreach ($row as $idx => $column) $head[$column] = $idx;
			while (false !== ($row = fgetcsv($handle))) {
				$index++;
				$record = array();
				$updating = false;
				foreach ($fields as $field) {
					$record[$field['destination']] = $row[$head[$field['source']]];
				}
				if (!empty($keys)) {
					$query = $this->db->query($import['model']);
					foreach ($keys as $key) $query->condition($import['model'].".".$key, $record[$key]);
					$exists = $query->one();
					if ($exists) {
						$record['id'] = $exists['id'];
						$updating = true;
					}
				}
				$this->models->get($import['model'])->{$import['action']}($record);
				if ($this->models->get($import['model'])->errors()) {
					$errors[$index] = $this->models->get($import['model'])->errors(false, true);
					$this->db->errors = array();
				} else if ($updating) {
					$updated++;
				} else {
					$created++;
				}
			}
		}
	}
}
