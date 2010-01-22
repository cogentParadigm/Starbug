<?php
efault($_POST['uris']['layout'], "2-col-right");
$layouts = array();
if (false !== ($handle = opendir("app/views/layouts/"))) {
	while (false !== ($file = readdir($handle))) {
		if ((strpos($file, ".") !== 0)) {
			$name = substr($file, 0, strpos($file, "."));
			//$layouts[$name] = $name;
			$layout = file_get_contents("app/views/layouts/".$file);
			$start = strpos($layout, "* containers:")+13;
			$end = strpos($layout, "\n", $start);
			$layouts[$name] = explode(",", trim(substr($layout, $start, $end-$start)));
		}
	}
	closedir($handle);
}
$containers = $layouts[$_POST['uris']['layout']];
echo $form->select("layout", $layouts);
