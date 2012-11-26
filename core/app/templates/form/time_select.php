<?php
	echo $this->select($field."[hour]  id:".$id."-hour  nolabel:  nodiv:", $hour_options);
	echo $this->select($field."[minutes]  id:".$id."-minutes  nolabel:  nodiv:", $minutes_options);
	echo $this->select($field."[ampm]  id:".$id."  nolabel:  nodiv:", $ampm_options);
?>
