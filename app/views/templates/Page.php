<?php
/* options: layout
 * cascade: disabled
 */
$name = current($this->uri);
$options = (isset($this->payload['options'])) ? unserialize($this->payload['options']) : array("layout" => "2-col-right");
include("app/views/layouts/$options[layout].php");
?>
