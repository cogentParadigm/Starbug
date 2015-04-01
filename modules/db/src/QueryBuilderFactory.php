<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file modules/db/src/QueryBuilderFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
/**
* an implementation of MacroInteface
*/
class QueryBuilderFactory implements QueryBuilderFactoryInterface {

	public function build(DatabaseInterface $db, $collection) {
		return new QueryBuilder($db, $collection);
	}

}
?>
