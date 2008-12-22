<?php
$file = fopen(dirname(__FILE__)."/../../app/models/".ucwords($argv[2]).".php", "w");
fwrite($file, "<?php\nclass ".ucwords($argv[2])." extends Table {\n\n");
if (file_exists(dirname(__FILE__)."/../../core/db/schema/".ucwords($argv[2]))) {
	$uniques = array(); $defaults => array(); $lengths => array();
	$fields = unserialize(file_get_contents(dirname(__FILE__)."/../../core/db/schema/".ucwords($argv[2])));
	foreach($fields as $name => $options) {
		if (isset($options['default'])) $defaults[$name] = $options['default'];
		if (isset($options['unique'])) $uniques[] = $name;
		if (isset($options['length'])) $lengths[$name] = $options['length'];
	}
	$d = ""; $l = "";
	foreach ($defaults as $name => $val) $d = (empty($d) ? "'".$name."' => '".$val."'" : $d.", '".$name."' => '".$val."'");
	foreach ($lengths as $name => $val) $l = (empty($l) ? "'".$name."' => '".$val."'" : $l.", '".$name."' => '".$val."'");
	fwrite($file, "\t".'var $defaults = array('.$d.");\n");
	fwrite($file, "\t".'var $uniques = array('.implode(", ", $uniques).");\n");
	fwrite($file, "\t".'var $lengths = array('.$l.");\n\n");
}
fwrite($file, "\tfunction create() {\n\t\tif (\$_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');\n\t\t\$".strtolower($argv[2])." = \$_POST['".strtolower($argv[2])."'];\n\t\treturn \$this->store(\$".strtolower($argv[2]).");\n\t}\n\n\tfunction delete() {\n\t\tif (\$_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');\n\t\treturn \$this->remove(\"id='\".\$_POST['".strtolower($argv[2])."']['id'].\"'\");\n\t}\n\n}\n?>");
fclose($file);
?>