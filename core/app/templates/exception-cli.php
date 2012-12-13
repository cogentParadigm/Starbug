---------------------------------------------------------------------------------------------------------------------------
<?php echo $error['message']."\n"; ?>
---------------------------------------------------------------------------------------------------------------------------
Stack Trace
<?php foreach ($error['traces'] as $n => $trace) { ?>
#<?php echo $n.' - '; ?>
<?php
	echo ' ';
	echo ($n == 0) ? $error['file']."(".$error['line'].")" : $trace['file']."(".$trace['line'].")";
	if (isset($trace['function'])) {
		echo ': ';
		if(!empty($trace['class'])) echo "{$trace['class']}{$trace['type']}";
		echo "{$trace['function']}(";
		if(!empty($trace['args'])) echo ErrorHandler::argumentsToString($trace['args']);
		echo ')';
		echo "\n";
	}
?>
<?php } ?>
---------------------------------------------------------------------------------------------------------------------------
