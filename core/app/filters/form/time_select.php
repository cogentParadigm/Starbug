<?php
	//GET POSTED OR DEFAULT VALUE
	$value = $this->get($ops['name']);
	efault($value, $ops['default']);
	if ((!empty($value)) && (!is_array($value))) {
		$dt = strtotime($value);
		$this->set($ops['name'], array("hour" => date("h", $dt), "minutes" => date("i", $dt), "ampm" => date("a", $dt)));
	}
	//SETUP OPTION ARRAYS
	$hour_options = array("Hour" => "-1");
	for($i=1;$i<13;$i++) $hour_options[$i] = $i;
	$minutes_options = array("Minutes" => "-1", "00" => "00", "15" => "15", "30" => "30", "45" => "45");
	$ampm_options = array("AM" => "am", "PM" => "pm");
	$this->assign("hour_options", $hour_options);
	$this->assign("minutes_options", $minutes_options);
	$this->assign("ampm_options", $ampm_options);
?>
