<?php
/**
* FILE: script/_generate/model.php
* PURPOSE: generates a model that extends the Table class
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
$filename = dirname(__FILE__)."/../../app/models/".ucwords($argv[2]).".php";
$file = fopen($filename, "w");
fwrite($file, "<?php\nclass ".ucwords($argv[2])." extends Table {\n\n");
if (file_exists(dirname(__FILE__)."/../../core/db/schema/".$argv[2])) {
	$uniques = array(); $defaults = array(); $lengths = array(); $timestamps = array(); $datetimes = array(); $bools = array();
	$fields = unserialize(file_get_contents(dirname(__FILE__)."/../../core/db/schema/".$argv[2]));
	foreach($fields as $name => $options) {
		if (isset($options['default'])) $defaults[$name] = $options['default'];
		if (isset($options['unique'])) $uniques[] = "\"$name\"";
		if (isset($options['length'])) $lengths[$name] = $options['length'];
		if ($options['type'] == "timestamp") $timestamps[] = $name;
		if ($options['type'] == "datetime") $datetimes[$name] = $options;
		if ($options['type'] == "bool") $bools[] = $name;
	}
	$d = ""; $l = "";
	foreach ($defaults as $name => $val) $d = (empty($d) ? "'".$name."' => '".$val."'" : $d.", '".$name."' => '".$val."'");
	foreach ($lengths as $name => $val) $l = (empty($l) ? "'".$name."' => '".$val."'" : $l.", '".$name."' => '".$val."'");
	fwrite($file, "\t".'var $defaults = array('.$d.");\n");
	fwrite($file, "\t".'var $uniques = array('.implode(", ", $uniques).");\n");
	fwrite($file, "\t".'var $lengths = array('.$l.");\n\n");
}
$create_func = "\tfunction create() {\n\t\tif (\$_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');\n\t\t\$".strtolower($argv[2])." = \$_POST['".strtolower($argv[2])."'];\n";
foreach($bools as $abdul) $create_func .= "if (empty(\$$argv[2]['$adbul'])) \$$argv[2]['$abdul'] = 0;\n";
foreach($timestamps as $stampfield) $create_func .= "\t\t\$$argv[2]['$stampfield'] = date(\"Y-m-d H:i:s\");\n";
foreach($datetimes as $datefield => $dateoptions) {
	$create_func .= "\t\t\${$datefield}d = \$$argv[2]['{$datefield}'];\n";
	if (!empty($options['time_select'])) {
		$create_func .= "\t\t\${$datefield}t = \$$argv[2]['{$datefield}_time'];\n";
		$create_func .= "\t\tunset(\$$argv[2]['{$datefield}_time']);\n";
		$create_func .= "\t\tif (\${$datefield}t['ampm'] == 'pm') \${$datefield}t['hour'] += 12;\n";
		$create_func .= "\t\t\$$argv[2]['{$datefield}'] = \"\${$datefield}d[year]-\${$datefield}d[month]-\${$datefield}d[day] \${$datefield}t[hour]:\${$datefield}t[minutes]:00\";\n";
		$create_func .= "\t\tif ((\${$datefield}t['hour'] == -1) || (\${$datefield}d['minutes'] == -1)) \$$argv[2]['{$datefield}'] = \"\";\n";
	} else $create_func .= "\t\t\$$argv[2]['{$datefield}'] = \"\${$datefield}d[year]-\${$datefield}d[month]-\${$datefield}d[day] 00:00:00\";\n";
}
$create_func .= "\t\tif ((\${$datefield}d['year'] == -1) || (\${$datefield}d['month'] == -1) || (\${$datefield}d['day'] == -1)) \$$argv[2]['{$datefield}'] = \"\";\n";
$create_func .= "\t\t\$_POST['$argv[2]']['$datefield'] = \$$argv[2]['$datefield'];\n";
$create_func .= "\t\treturn \$this->store(\$".strtolower($argv[2]).");\n\t}\n\n"; 
fwrite($file, $create_func."\tfunction delete() {\n\t\tif (\$_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');\n\t\treturn \$this->remove(\"id='\".\$_POST['".strtolower($argv[2])."']['id'].\"'\");\n\t}\n\n}\n?>");
fclose($file);
$infofile = dirname(__FILE__)."/../../core/db/schema/.info/$argv[2]";
if (file_exists($infofile)) $info = unserialize(file_get_contents($infofile));
else $info = array();
$info['mtime'] = filemtime($filename);
$file = fopen($infofile, "wb");
fwrite($file, serialize($info));
fclose($file);
?>
