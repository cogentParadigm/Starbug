<input<? foreach ($attributes as $key => $value) if (!empty($value) && !is_array($value) && !in_array($key, array("label", "field"))) echo ' '.$key.'="'.$value.'"'; ?>/>
