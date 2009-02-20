<?php
/**
* FILE: util/HT.php
* PURPOSE: XHTML generation utility
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
include('XML.php');
class HT extends XML {

	public function doctype() {
		return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n";
	}

	public function xhtml($content) {
		return HT::tag("html,xmlns=http://www.w3.org/1999/xhtml,xml:lang=en", $content);
	}

	public function meta($name, $content) {
		return HT::noclose("meta,name=".$name.",content=".$content);
	}

	public function headr($htequiv, $content) {
		return HT::noclose("meta,http-equiv=".$htequiv.",content=".$content);
	}

	public function lnk($rel, $href) {
		if (substr($rel, 0, 10)=="stylesheet") $href .= ",type=text/css";
		return HT::noclose("link,rel=".$rel.",href=".$href);
	}

	public function lis($type, $content) {
		$lies = HT::open($type);
		foreach ($content as $item) $lies .= HT::tag("li", $item);
		return $lies.HT::close($type);
	}

	public function dl($content, $attrs="") {
		$dls = ((empty($attrs))?HT::open("dl"):HT::open("dl,".$attrs));
		foreach ($content as $pair) {
			$pair = $pair.split("\n");
			$dls .= HT::tag("dt", $pair[0]);
			$dls .= HT::tag("dd", $pair[1]);
		}
		return $dls.HT::close("dl");
	}

	public function img($src, $alt) {
		return HT::noclose("img,src=".$source.",alt=".$alt);
	}

	public function a($href, $content) {
		return HT::tag("a,href=".Etc::WEBSITE_URL.$href, $content);
	}

}
