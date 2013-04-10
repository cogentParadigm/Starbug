<?php
//converts a string into a url slug and stores that in another field
foreach($args as $field => $slug_field) {
	$fields[$slug_field] = strtolower(str_replace(" ", "-", normalize($fields[$field])));
}
?>
