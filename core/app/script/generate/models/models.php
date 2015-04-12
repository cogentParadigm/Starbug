<?php
	foreach ($this->schemer->tables as $name => $fields) passthru("./sb generate model $name -u");
?>
