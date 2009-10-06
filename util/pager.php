<?php
/**
* FILE: util/pager.php
* PURPOSE: pagination class
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
$sb->provide("util/pager");
class pager {
	var $items;
	var $count;
	var $per;
	var $last;
	var $start;
	var $finish;
	var $current_page;
	var $current_item;
	function pager($item_array, $total_items, $items_per_page, $current_pg=1) {
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
	function links($prefix) {
		if ($this->count > 25) { ?>
			<ul class="pages">
				<?php if ($this->current > 1) { ?>
				<li class="back"><a href="<?php echo uri($prefix.($this->current-1)); ?>">Back</a></li>
				<?php } for($i=1;$i<=$this->last;$i++) { ?>
				<li><a <?php if($this->current == $i) { ?>class="active" <?php } ?>href="<?php echo uri($prefix.$i); ?>"><?php echo $i; ?></a></li>
				<?php } if ($this->current < $this->last) { ?>
				<li class="next"><a href="<?php echo uri($prefix.($this->current+1)); ?>">Next</a></li>
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
