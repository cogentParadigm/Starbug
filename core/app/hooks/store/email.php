<?php
class hook_store_email {
  function validate(&$query, $key, $value, $column, $argument) {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) error("Please enter a valid email address.", $column);
    return $value;
  }
}
?>
