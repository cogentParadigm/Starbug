<?php
abstract class Migration {

	private $db;

	function Migration($data) {
		$this->db = $data;
	}

	function create_table($name, $fields) {
		$sql = "DROP TABLE IF EXISTS `".P($name)."`";
		$this->db->Execute($sql);
		$sql = "CREATE TABLE `".P($name)."` (";
		$sql .= "id int(11) NOT NULL AUTO_INCREMENT, ";
		foreach ($fields as $name => $options) {
			$type = "int(11)";
			if ($options['type'] == 'tiny_string') $type = "varchar(2)";
			else if ($options['type'] == 'mini_string') $type = "varchar(3)";
			else if ($options['type'] == 'short_string') $type = "varchar(16)";
			else if ($options['type'] == 'string') $type = "varchar(32)";
			else if ($options['type'] == 'long_string') $type = "varchar(64)";
			else if ($options['type'] == 'longer_string') $type = "varchar(128)";
			else if ($options['type'] == 'huge_string') $type = "varchar(256)";
			else if ($options['type'] == 'small_int') $type = "int(2)";
			else if ($options['type'] == 'int') $type = "int(11)";			
			else if ($options['type'] == 'datetime') $type = "datetime";
			else if ($options['type'] == 'timestamp') $type = "timestamp";
			$sql .= $name." ".$type." NOT NULL".((!isset($options['default'])) ? "" : " default '".$options['default']."'").", ";
		}
		$sql .= "security int(2) NOT NULL default '2', PRIMARY KEY (`id`) ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;";
		$result = $this->db->Execute($sql);
		$this-create_model(ucwords($name),$fields);
	}
	

	function drop_table($name) {$this->db->Execute("DROP TABLE IF EXISTS `".P($name)."`;");}

	function table_insert($table, $keys, $values) {$this->db->Execute("INSERT INTO ".P($table)." (".$keys.") VALUES (".$values.")");}

	function create_model($name, $fields) {
		$file = null;
		$exists = false;
		if (file_exists(dirname(__FILE__)."/../../app/models/".$name.".php")) {
			$exists = true;
			$file = fopen(dirname(__FILE__)."/../../app/models/".$name.".php", "r+");
		} else {
			$file = fopen(dirname(__FILE__)."/../../app/models/".$name.".php", "w");
		}
		fwrite($file, "<?php\nclass ".$name." extends Table {\n\n");
		$uniques = array(); $defaults => array(); $lengths => array();
		foreach($fields as $name => $options) {
			if (isset($options['default'])) $defaults[$name] = $options['default'];
			if (isset($options['unique'])) $uniques[] = $name;
			if (isset($options['length'])) $lengths[$name] = $options['length'];
		}
		$d = ""; $l = "";
		foreach ($defaults as $name => $val) $d = (empty($d) ? "'".$name."' => '".$val."'" : $d.", '".$name."' => '".$val."'");
		foreach ($lengths as $name => $val) $l = (empty($l) ? "'".$name."' => '".$val."'" : $l.", '".$name."' => '".$val."'");
		fwrite($file, 'var $defaults = array('.$d.");\n");
		fwrite($file, 'var $uniques = array('.implode(", ", $uniques).");\n");
		fwrite($file, 'var $lengths = array('.$l.");\n");
		if (!$exists) fwrite($file, "\n}\n?>");
		fclose($file);
	}

	function draw_form($name) {
		$file = null;
		$file = fopen(dirname(__FILE__)."/../../app/results/".$name."_form.php", "w");
		fwrite($file, Form::render($this->describe()));
		fclose($file);
	}

	abstract function describe();

	abstract function up();

	abstract function down();

}
?>
