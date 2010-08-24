<?php
/**
* FILE: util/lister.php
* PURPOSE: lister class - outputs lists of data in an HTML table
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
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
$sb->provide("util/lister");
class lister {
	var $items;
	var $headers;
	var $ops;
	var $pager;
	var $url;
	function lister($ops, $url, $cols="", $itemlist=array()) {
		$this->ops = starr::star($ops);
		$this->url = $url;
		$this->items = $itemlist;
		$this->headers = starr::star($cols);
		$this->ops['orderby'] = (isset($this->ops['orderby'])) ? explode(" ", $this->ops['orderby']) : array("", "");
	}
	function add_columns($str) {
		$this->headers = array_merge($this->headers, starr::star($str));
	}
	function add_column($str) {
		$str = starr::star($str);
		$this->headers[array_shift($str)] = $str;
	}
	function items($arr) {
		global $sb;
		$this->items = $arr;
		if ($this->ops['show']) {
			$sb->import("util/pager");
			$this->pager = new pager($this->items, $this->ops['show'], $this->ops['page']);
			$this->items = array_slice($this->pager->items, $this->pager-start, $this->pager->finsh-$this->pager->start);
		}
	}
	function query($froms, $args, $mine=false) {
		global $sb;
		$this->items($sb->query($froms, $args."  orderby:".$this->ops['orderby'][0], $mine));
	}
	function render($tag) {
		$lsearch = array('[orderby]', '[direction]');
		$lreplace = array($this->ops['orderby'][0], $this->ops['orderby'][1]);
		$pager_url = explode("[page]", str_replace($lsearch, $lreplace, $this->url));
		$this->pager->links($pager_url[0], $pager_url[1]);
		$sort_url = str_replace("[page]", $this->ops['page'], $this->url);
		$output = tag("table  echo:false  ".$tag);
		$output = str_replace("</table>", "", $output);
		$th = "";
		foreach(array("thead", "tfoot") as $t) {
			$th .= "<$t><tr>";
			foreach($this->headers as $key => $value) {
				if (!isset($value['caption'])) $value['caption'] = ucwords(str_replace("_", " ", $key));
				$arg = starr::star("th  echo:false  content:".$value['caption']);
				efault($arg['class'], str_replace(array(" ", "_"), array("-", "-"), strtolower($key))."-col");
				if (isset($value['sortable'])) {
					$surl = str_replace("[orderby]", $key, $sort_url);
					if (($this->ops['orderby'][0]==$key) && ($this->ops['orderby'][1] == "ASC")) {
						$surl = str_replace("[direction]", "DESC", $surl);
						$token = "&#916;";
					} else {
						$surl = ($this->ops['orderby'][0] == $key) ? str_replace("[direction]", "ASC", $surl) : str_replace("[direction]", "DESC", $surl);
						$token = "&#8711;";
					}
					$arg['content'] .= ' <a'.(($this->ops['orderby'][0]==$key) ? ' class="active"' : '').' href="'.$surl.'">'.$token.'</a>';
				}
				$th .= tag($arg);
			}
			$th .= "</tr></$t>";
		}
		echo $output.$th;
		$renderer = $this->ops['renderer'];
		global $request;
		foreach($this->items as $item) include($request->payload['prefix']."renderers/$renderer.php");
		echo "</table>";
	}
}
