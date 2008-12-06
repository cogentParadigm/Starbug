<?php
class XML {

	public function open($tag, $close=false) {
		$comma = strpos($tag, ",");
		$attstr = "";
		if (!($comma === false)) {
			$attarr = split(",", substr($tag,$comma+1));
			foreach ($attarr as $keypair) {
				if (empty($keypair)) continue;
				$keypair = split("=", $keypair);
				$attstr.= ' '.$keypair[0].'="'.str_replace("&eq;", "=", $keypair[1]).'"';
			}
			$tag = substr($tag, 0, $comma);
		}
		return "<".$tag.$attstr.(($close==true) ? "/>" : ">");
	}

	public function close($tag) {
		$comma = strpos($tag, ",");
		if (!($comma === false)) $tag = substr($tag, 0, $comma);
		return "</".$tag.">";
	}

	public function tag($name, $content) {
		return XML::open($name).$content.XML::close($name);
	}

	public function noclose($tag) {
		return XML::open($tag, true);
	}

}