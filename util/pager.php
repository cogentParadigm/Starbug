<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/pager.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup pager
 */
/**
 * @defgroup pager
 * pager utility
 * @ingroup util
 */
$sb->provide("util/pager");
/**
 * pagination class
 * @ingroup pager
 */
class pager {
	var $items;
	var $count;
	var $per;
	var $last;
	var $start;
	var $finish;
	var $current_page;
	var $current_item;
	function pager($item_array, $items_per_page, $current_pg=1, $total_items=null) {
		global $sb;
		if ($total_items == null) $total_items = count($item_array);
		$this->items = $item_array;
		$this->count = $total_items;
		$this->per = $items_per_page;
		empty_nan($current_pg, 1);
		$this->current_page = $current_pg;
		$this->last = ceil($total_items/$items_per_page);
		$this->start = (($current_pg-1)*$items_per_page);
		$remaining = $total_items - $this->start;
		$shown = ($remaining<$items_per_page) ? ($remaining % $items_per_page) : $items_per_page;
		$this->finish = $this->start + $shown;
		$this->current_item = $this->start;
	}
	function reset() {$this->current_item = $this->start;}
	function links($prefix, $suffix="") {
		if ($this->count > $this->per) { ?>
			<ul class="pages hnav">
				<?php if ($this->current_page > 1) { ?>
				<li class="back"><a class="button" href="<?php echo $prefix.($this->current_page-1).$suffix; ?>">Back</a></li>
				<?php } for($i=1;$i<=$this->last;$i++) { ?>
				<li><a class="button<?php if($this->current_page == $i) { ?> active<?php } ?>" href="<?php echo $prefix.$i.$suffix; ?>"><?php echo $i; ?></a></li>
				<?php } if ($this->current_page < $this->last) { ?>
				<li class="next"><a class="button" href="<?php echo $prefix.($this->current_page+1).$suffix; ?>">Next</a></li>
				<?php } ?>
			</ul>
		<?php }
	}
	function item() {
		if ($this->current_item < $this->finish) {
			$this->current_item++;
			return $this->items[($this->current_item-1)];
		} else return false;
	}
	function position() {return ($this->current_item-$this->start); }
}
