<?php
/**
* generates a model that extends the Table class
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
$file = fopen(dirname(__FILE__)."/../../app/models/".ucwords($argv[2]).".php", "w");
fwrite($file, "<?php\nclass ".ucwords($argv[2])." extends Table {\n\n");
if (file_exists(dirname(__FILE__)."/../../core/db/schema/".ucwords($argv[2]))) {
	$uniques = array(); $defaults = array(); $lengths = array();
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