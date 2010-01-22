<?php
/* options: layout
 * cascade: disabled
 */
$name = current($this->uri);
$options = unserialize($this->payload['options']);
include("app/views/layouts/$options[layout].php");
?>
