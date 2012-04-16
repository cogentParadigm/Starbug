<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/lister.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup lister
 */
/**
 * @defgroup lister
 * lister utility
 * @ingroup util
 */
$sb->provide("util/lister");
/**
 * outputs lists of data in an HTML table
 * @ingroup lister
 */
class lister {
	var $items;
	var $headers;
	var $ops;
	var $pager;
	var $url;
	function lister($ops, $url, $cols="", $itemlist=array()) {
		$this->ops = star($ops);
		$this->url = $url;
		$this->items = $itemlist;
		$this->headers = star($cols);
		$this->ops['orderby'] = (isset($this->ops['orderby'])) ? explode(" ", $this->ops['orderby']) : array("", "");
	}
	function add_columns($str) {
		$this->headers = array_merge($this->headers, star($str));
	}
	function add_column($str) {
		$str = star($str);
		$this->headers[array_shift($str)] = $str;
	}
	function items($arr) {
		global $sb;
		$this->items = $arr;
		if ($this->ops['show']) {
			$sb->import("util/pager");
			$this->pager = new pager($this->items, $this->ops['show'], $this->ops['page']);
			$this->items = array_slice($this->pager->items, $this->pager->start, $this->pager->finish - $this->pager->start);
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
				$arg = star("th  echo:false  content:".$value['caption']);
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
