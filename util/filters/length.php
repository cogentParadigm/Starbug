<?php
//length
foreach($args as $field => $length) {
	$length = explode("-", $length);
	if (!next($length)) $length = array(0, $length[0]);
	$val_length = strlen($fields[$field]);
	if(!($val_length >= $length[0] && $val_length <= $length[1])) $errors[$field."LengthError"] = true;
}
?>
