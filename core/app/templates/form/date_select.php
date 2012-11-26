<?php
	echo $this->select($field."[month]  id:".$id."-mm  nolabel:  nodiv:", $month_options);
	echo $this->select($field."[day]  id:".$id."-dd  nolabel:  nodiv:", $day_options);
	echo $this->select($name."[year]  id:".$id."  class:split-date range-low-".date("Y-m-d")." no-transparency  nolabel:  nodiv:", $year_options);
	//TIME
	if (!empty($time_select)) echo $this->time_select(array_merge(array($field), $attributes));
?>
