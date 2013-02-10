<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file modules/db/hooks/mysql.router.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 * mysql based router
 *
 * This is run from core/Request.php
 * $this->path is set, you must use it to look up the route in the uris table
 * 
 * IF THE USER HAS ACCESS TO VIEW IT:
 * a router hook must set $this->payload, and $this->tags to the record/document and it's tags respectively
 * you must also set $this->tags
 * 
 * IF THE ROUTE EXIST, BUT THE USER DOES NOT HAVE PERMISSION TO VIEW IT:
 * call $this->forbidden()
 * 
 * IF THE ROUTE DOES NOT EXIST:
 * call $this->missing()
 */
		//set up a query for uris where the path is a prefix of the current path
		$query = "select:uris.*  where:(uris.status & 4) && '".$this->path."' LIKE CONCAT(path, '%') ORDER BY CHAR_LENGTH(path) DESC  limit:1";
		//run the query, looking for read permits
		$this->payload = query("uris", $query."  action:read");
		if (empty($this->payload)) { //if we find nothing, query without looking for permits
			$row = query("uris", $query);
			if (!empty($row)) $this->forbidden(); //if we find something that means we don't have permission to see it, so show the forbidden page
			else $this->missing(); //if we don't find anything, there is nothing there, so show the missing page
		}
		$this->tags = array_merge($this->tags, query("uris,terms via uris_tags", "select:DISTINCT term, slug  where:uris.id='".$this->payload['id']."'"));
?>
