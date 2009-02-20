<?php
/**
* FILE: util/XML.php
* PURPOSE: XML generation utility
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
