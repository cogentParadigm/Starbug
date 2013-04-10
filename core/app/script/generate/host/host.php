<?php
	if (!defined("BASE_DIR")) define("BASE_DIR", str_replace("core/app/script/generate/host", "", dirname(__FILE__)));
	$conf = json_decode(str_replace('%HMAC_KEY%', md5(uniqid(rand(), TRUE)), file_get_contents(BASE_DIR."/etc/host.json")), true);
	$host = (file_exists(BASE_DIR."/etc/Host.php")) ? file_get_contents(BASE_DIR."/etc/Host.php") : $migration = file_get_contents(BASE_DIR."/core/app/script/generate/host/BlankHost.php");
	foreach ($conf as $name => $value) {
		if (false === strpos($host, "const ".$name)) {
			$replace = "";
			if (is_array($value)) {
				$replace = "\n\t/* ".$value[1]." */\n";
				$value = $value[0];
			}
			$replace .= "\tconst ".$name." = \"".$value."\";\n}";
			$pos = strrpos($host, "}");
			$host = substr_replace($host, $replace, $pos, 1); 
		}
	}
	file_put_contents(BASE_DIR."/etc/Host.php", $host);
	exit(0);
?>
