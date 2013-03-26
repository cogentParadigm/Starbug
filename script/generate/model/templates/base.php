<? extract(schema($model)); echo '<?php'."\n"; ?>
/**
 * <?= $name; ?> model base
 * @ingroup models
 */
class <?= ucwords($name); ?>Model extends Table {

	var $filters = array(<? $count = 0; foreach ($fields as $column => $field) { if (!empty($field['filters'])) { if ($count > 0) echo ','; echo "\n"; ?>
		"<?= $column; ?>" => "<? foreach ($field['filters'] as $k => $v) { ?><? if ($count > 0) echo "  "; $count++ ?><?= $k; ?>:<?= $v; ?><? } ?>"<? } } echo "\n"; ?>
	);

	function init() {<? foreach ($fields as $column => $field) { foreach ($field['filters'] as $k => $v) { if ($k == "references") { $v = explode(" ", $v); echo "\n"; ?>
		$this->has_one("<?= $v[0]; ?>", "<?= $column; ?>");<? } } } ?><? foreach ($relations as $relation) { echo "\n"; ?>
		$this->has_many("<?= $relation['model']; ?>", "<?= $relation['field']; ?>"<? if (!empty($relation['lookup'])) { ?>, "<?= $relation['lookup']; ?>", "<?= $relation['ref_field']; ?>"<? } ?>);<? } echo "\n"; ?>
	}
	
	function create($<?= $singular; ?>) {
		$this->store($<?= $singular; ?>);
	}

	function delete($<?= $singular; ?>) {
		return $this->store(array('status' => 1,  'id' => $<?= $singular; ?>['id']));
	}
	
	function query_admin($query) {
		$query['where'][] = "!(<?= $name; ?>.status & 1)";
		return $query;
	}
	
	function query_get($query) {
		return $query;
	}
	
	function query_select($query) {
		$query['where'][] = "!(<?= $name; ?>.status & 1)";
		$query['select'] = "<?= $name; ?>.id,<?= efault($label_select, $name.".id"); ?> as label";
		return $query;
	}

}
<?= '?>'; ?>
