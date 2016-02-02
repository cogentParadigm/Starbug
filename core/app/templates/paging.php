<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/templates/paging.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup templates
 * renders links for paging
 */
	$vars = $this->request->getParameters();
	unset($vars['page']);
	$prefix = uri($request->getPath())."?";
	if (!empty($vars)) $prefix .= http_build_query($vars).'&';
	$prefix .= "page=";

	$half = floor($request->pager->range/2);
	//set $from to $current_page minus half of $range OR 1
	$from = ($request->pager->current_page > $half) ? $request->pager->current_page-$half : 1;
	//set $to to the full range from from
	$to = $from + $request->pager->range;
	//if that pushes us past the end, shift back to the end
	if ($to > $request->pager->last) {
		$to = $request->pager->last;
		$from = $to - $request->pager->range;
	}
	//if there are not enough pages, bring up $from to 1
	if ($from < 1) $from = 1;

	if ($request->pager->count > $request->pager->per) { ?>
		<ul class="pages hnav">
			<?php if ($request->pager->current_page > 1) { ?>
			<li class="back"><a class="button" href="<?php echo $prefix.($request->pager->current_page-1).$suffix; ?>">Back</a></li>
			<?php } if ($from > 1) { ?>
			<li class="first"><a class="button" href="<?php echo $prefix."1".$suffix; ?>">1</a><div style="padding-top:18px;float:left">...</div></li>
			<?php } for($i=$from;$i<=$to;$i++) { ?>
			<li><a class="button<?php if($request->pager->current_page == $i) { ?> active<?php } ?>" href="<?php echo $prefix.$i.$suffix; ?>"><?php echo $i; ?></a></li>
			<?php } if ($to < $request->pager->last) { ?>
			<li class="last"><div style="padding:18px 8px 0px 0px;float:left">...</div><a class="button" href="<?php echo $prefix.$request->pager->last.$suffix; ?>"><?php echo $request->pager->last; ?></a></li>
			<?php } if ($request->pager->current_page < $request->pager->last) { ?>
			<li class="next"><a class="button" href="<?php echo $prefix.($request->pager->current_page+1).$suffix; ?>">Next</a></li>
			<?php } ?>
		</ul>
	<?php } ?>
