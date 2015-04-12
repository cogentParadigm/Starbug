<?php
	import("datepicker");
	//FILL VALUES FROM POST OR DEFAULT
	$name = $field['name'];
	$value = $this->get($name);
	efault($value, $field['default']);
	if ((!empty($value)) && (!is_array($value))) {
		$dt = strtotime($value);
		$this->set($name, array("year" => date("Y", $dt), "month" => date("m", $dt), "day" => date("d", $dt)));
		if (!empty($field['time_select'])) $this->set($name."_time", array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt)));
	}
	//SETUP OPTION ARRAYS
	$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
	$day_options = array("Day" => "-1");
	for($i=1;$i<32;$i++) $day_options["$i"] = $i;
	$start_year = ($field['start_year']) ? $field['start_year'] : date("Y");
	$end_year = ($field['end_year']) ? $field['end_year'] : $start_year+2;
	$year_options = array("Year" => "-1");
	if ($start_year < $end_year) for ($i=$start_year;$i<=$end_year;$i++) $year_options[$i] = $i;
	else for ($i=$start_year;$i>=$end_year;$i--) $year_options[$i] = $i;
	$this->assign("day_options", $day_options);
	$this->assign("month_options", $month_options);
	$this->assign("year_options", $year_options);
?>
