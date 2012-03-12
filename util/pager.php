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
	var $count; //total number of items
	var $per; //items per page
	var $last; //last page
	var $start; // showing $start to $finish of $count items
	var $finish; // showing $start to $finish of $count items
	var $current_page; //current page
	var $range; //number of page links to show
	function pager($total, $items_per_page, $current_pg=1, $range=6) {
		$this->count = $total;
		$this->per = $items_per_page;
		$this->range = $range;
		empty_nan($current_pg, 1);
		$this->current_page = $current_pg;
		$this->last = ceil($total/$items_per_page);
		$this->start = (($current_pg-1)*$items_per_page);
		if ($this->current_page < 1) $this->current_page = 1;
		else if ($this->current_page > $this->last) $this->current_page = $this->last;
		$remaining = $total - $this->start;
		$shown = ($remaining<$items_per_page) ? ($remaining % $items_per_page) : $items_per_page;
		$this->finish = $this->start + $shown;
	}
	function links($prefix, $suffix="") {
		$half = floor($this->range/2);
		//set $from to $current_page minus half of $range OR 1
		$from = ($this->current_page > $half) ? $this->current_page-$half : 1;
		//set $to to the full range from from
		$to = $from + $this->range;
		//if that pushes us past the end, shift back to the end
		if ($to > $this->last) {
			$to = $this->last;
			$from = $to - $this->range;
		}
		//if there are not enough pages, bring up $from to 1
		if ($from < 1) $from = 1;
		
		if ($this->count > $this->per) { ?>
			<ul class="pages hnav">
				<?php if ($this->current_page > 1) { ?>
				<li class="back"><a class="button" href="<?php echo $prefix.($this->current_page-1).$suffix; ?>">Back</a></li>
				<?php } if ($from > 1) { ?>
				<li class="first"><a class="button" href="<?php echo $prefix."1".$suffix; ?>">1</a><div style="padding-top:18px;float:left">...</div></li>
				<?php } for($i=$from;$i<=$to;$i++) { ?>
				<li><a class="button<?php if($this->current_page == $i) { ?> active<?php } ?>" href="<?php echo $prefix.$i.$suffix; ?>"><?php echo $i; ?></a></li>
				<?php } if ($to < $this->last) { ?>
				<li class="last"><div style="padding:18px 8px 0px 0px;float:left">...</div><a class="button" href="<?php echo $prefix.$this->last.$suffix; ?>"><?php echo $this->last; ?></a></li>
				<?php } if ($this->current_page < $this->last) { ?>
				<li class="next"><a class="button" href="<?php echo $prefix.($this->current_page+1).$suffix; ?>">Next</a></li>
				<?php } ?>
			</ul>
		<?php }
	}
}
