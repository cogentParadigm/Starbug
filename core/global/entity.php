<?php
/**
* getter/caller for model properties/functions
* @ingroup entity
*/
function sb() {
	$sb = sb::$instance;
	$args = func_get_args();
	$count = count($args);
	if ($count == 0) return $sb;
	else if ($count == 1) {
		if ($sb->models->has($args[0])) return $sb->models->get($args[0]);
		else return $sb->db->$args[0];
	} else if ($count == 2) {
		return $sb->models->get($args[0])->$args[1];
	} else {
		$model = $sb->models->get(array_shift($args));
		$function = array_shift($args);
		return call_user_func_array(array($model, $function), $args);
	}
}

/**
* get the root model of an entity
* @param string $entity the entity
* @return string the base model
*/
function entity_base($entity) {
	$base = $entity;
	while (!empty(sb($base)->base)) $base = sb($base)->base;
	return $base;
}

/**
* get an array representing the chain of inheritance for an entity
* @param string $entity the name of the entity
* @return array the inheritance chain. the first member will be $entity
*/
function entity_chain($entity) {
	$chain = array();
	while (!empty($entity)) {
		$chain[] = $entity;
		$entity = sb($entity)->base;
	}
	return $chain;
}

/**
* get entity or column info
* @param string $entity entity name
* @param string $column column name
*/
function column_info($entity, $column = "") {
	$info = array();
	if (!sb()->models->has($entity)) return $info;
	if (empty($column)) {
		while (!empty($entity)) {
			$hooks = sb($entity)->hooks;
			foreach ($hooks as $col => $hook) $hooks[$col]["entity"] = $entity;
			$info = array_merge($hooks, $info);
			$entity = sb($entity)->base;
		}
	} else {
		while (!isset(sb($entity)->hooks[$column]) && !empty(sb($entity)->base)) $entity = sb($entity)->base;
		if (isset(sb($entity)->hooks[$column])) $info = sb($entity)->hooks[$column];
		$info["entity"] = $entity;
	}
	return $info;
}

/**
* Query helper to provide a query with all tables joined and columns selected
* @ingroup entity
* @param string $entity the name of the entity
*/
function entity_query($entity) {
	$chain = array();
	$base = $entity;

	//build entity chain
	while (!empty($base)) {
		$chain[] = $base;
		$base = sb($base)->base;
	}

	//build query
	foreach ($chain as $idx => $name) {
		$collection = ($name === $entity) ? $entity : $entity."_".$name;
		if ($idx === 0) $query = query($name." as ".$collection);
		else {
			$query->join($name." as ".$collection, "INNER");
		}
	}

	//add selection
	$reverse = array_reverse($chain);
	foreach ($reverse as $idx => $name) {
		$collection = ($name === $entity) ? $entity : $entity."_".$name;
		$query->select("*", $collection);
	}

	return $query;
}

/**
* load an entity by id
* @ingroup entity
* @param string $name the name of the entity
* @param int $id the id of the entity to load
* @param boolean $reset set to true if you don't want to load from cache
*/
function entity_load($name, $id, $reset = false) {
	static $entities = array();
	if (is_array($id)) {
		$conditions = $id;
		$id = false;
	}
	if ($reset || !$id || !isset($entities[$id])) {
		if ($id) $entities[$id] = entity_query($name)->condition($name.".id", $id)->one();
		else if ($conditions) {
			$entity = entity_query($name)->conditions($conditions)->one();
			$id = $entity["id"];
			$entities[$id] = $entity;
		}
	}
	return $entities[$id];
}

/**
* save an entity
* @ingroup entity
* @param string $name the name of the entity
* @param array $fields the properties to save
* @param array $from the conditions to match on instead of an ID. must map to a single entity
*/
function entity_save($name, $fields, $from = array()) {
	$chain = array();
	$base = $name;
	$original = $update = false;

	if (!empty($fields["id"])) {
		$update = true;
		$original = entity_load($name, $fields["id"]);
	} else if (!empty($from)) {
		$original = entity_load($name, $from);
		if ($original) {
			$update = true;
			$fields["id"] = $original["id"];
		}
	}

	//build entity chain
	while (!empty($base)) {
		$chain[] = $base;
		$base = sb($base)->base;
	}

	$last = count($chain)-1;

	foreach ($chain as $idx => $name) {
		if ($idx < $last) {
			$record = array();
			foreach (sb($name)->hooks as $column => $hooks) {
				if ($column !== "id" && $column !== $chain[$last]."_id" && isset($fields[$column])) {
					$record[$column] = $fields[$column];
					unset($fields[$column]);
				}
			}
			if ($update) {
				if (!empty($record)) queue($name, $record, array($chain[$last]."_id" => $original[$chain[$last]."_id"]));
			} else {
				$record[$chain[$last]."_id"] = "";
				queue($name, $record);
			}
		} else {
			if ($last > 0) {
				unset($fields[$chain[$last]."_id"]);
				if ($update) $fields["id"] = $original[$chain[$last]."_id"];
			}
			store($name, $fields);
		}
	}
}

/**
* delete an entity by id
* @ingroup entity
* @param string $name the entity name
* @param int $id the id of the item to delete
*/
function entity_delete($name, $id) {
	$chain = array();
	$base = $name;
	$original = entity_load($name, $id);
	if (!$original) return;

	//build entity chain
	while (!empty($base)) {
		$chain[] = $base;
		$base = sb($base)->base;
	}

	$last = count($chain)-1;

	foreach ($chain as $idx => $name) {
		if ($idx < $last) {
			remove($name, array($chain[$last]."_id" => $original[$chain[$last]."_id"]));
		} else {
			remove($name, array("id" => $original[$name."_id"]));
		}
	}
}
?>
