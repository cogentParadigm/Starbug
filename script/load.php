<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/load.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$search = array(";");
$replace = array("\;");
$user = str_replace($search, $replace, Etc::DB_USERNAME);
$pass = str_replace($search, $replace, Etc::DB_PASSWORD);
$dbname = str_replace($search, $replace, Etc::DB_NAME);
$host = str_replace($search, $replace, Etc::DB_HOST);
exec("mysql -u $user --host=$host --password=$pass --database=$dbname < ".BASE_DIR."/var/dump/db.sql");
?>