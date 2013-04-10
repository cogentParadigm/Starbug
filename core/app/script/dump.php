<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/dump.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$tables = $sb->db->pdo->query("SHOW TABLES LIKE '".Etc::PREFIX."%'")->fetchAll(PDO::FETCH_ASSOC);
if (!file_exists(BASE_DIR."/var/dump")) {
	mkdir(BASE_DIR."/var/dump");
	chmod(BASE_DIR."/var/dump", 0777);
}
$search = array(";");
$replace = array("\;");
$user = str_replace($search, $replace, Etc::DB_USERNAME);
$pass = str_replace($search, $replace, Etc::DB_PASSWORD);
$dbname = str_replace($search, $replace, Etc::DB_NAME);
$host = str_replace($search, $replace, Etc::DB_HOST);
foreach($tables as $idx => $t) {
	$table = array_shift($t);
	echo "Dumping Table $table...\n";
	$op = (0 == $idx) ? ">>" : ">";
	exec("mysqldump -c --host=".$host." -u ".$user." -p".$pass." ".$dbname." ".$table." $op ".BASE_DIR."/var/dump/db.sql");
}
chmod(BASE_DIR."/var/dump/db.sql", 0777);
?>
