<?php
	$file = $this->db->query("files")->condition("id", $csv)->one();
	$$lines = 0;
	if (false !== ($handle = fopen("app/public/uploads/".$file['id']."_".$file['filename'], "r"))) {
		while (!feof($handle)) {
			if (fgets($handle)) $lines++;
		}
	}
	fclose($handle);
	$lines--;
	$size = 20;
	$pager = new \Starbug\Core\pager($lines, $size, intval($this->request->getParameter('pg')));
	$line = 0;
?>
<?php if (false !== ($handle = fopen("app/public/uploads/".$file['id']."_".$file['filename'], "r"))) { $head = fgetcsv($handle); ?>
<table <?php html_attributes($attributes); ?>>
<?php if (!empty($head)) { ?>
	<thead>
		<tr>
			<th>#</th><?php foreach ($head as $h) { ?><th><?php echo $h; ?></th><?php } ?>
		</tr>
	<thead>
<?php } ?>
<?php while ($row = fgetcsv($handle)) { ?>
		<?php $line++; ?>
		<?php if ($line <= $pager->start) continue; ?>
		<?php if ($line > $pager->finish) break; ?>
		<tr>
			<td><?php echo $line; ?></td>
			<?php foreach ($row as $v) { ?><td><?php echo $v; ?></td><?php } ?>
		</tr>
<?php } ?>
</table>
<?php } ?>
<?php
	$vars = $this->request->getParameters();
	unset($vars['pg']);
	$prefix = $this->request->getURL()->getDirectory();
	$prefix .= $this->request->getPath()."?";
	if (!empty($vars)) $prefix .= http_build_query($vars).'&';
	$prefix .= "pg=";

	$half = floor($pager->range/2);
	//set $from to $current_page minus half of $range OR 1
	$from = ($pager->current_page > $half) ? $pager->current_page-$half : 1;
	//set $to to the full range from from
	$to = $from + $pager->range;
	//if that pushes us past the end, shift back to the end
	if ($to > $pager->last) {
		$to = $pager->last;
		$from = $to - $pager->range;
	}
	//if there are not enough pages, bring up $from to 1
	if ($from < 1) $from = 1;
?>
<?php if ($lines > $size) { ?>
	<ul class="pagination">
		<?php if ($pager->current_page > 1) { ?>
			<li class="back"><a href="<?php echo $prefix.($pager->current_page-1).$suffix; ?>">Back</a></li>
			<?php } if ($from > 1) { ?>
			<li class="first"><a href="<?php echo $prefix."1".$suffix; ?>">1</a><div style="padding-top:18px;float:left">...</div></li>
			<?php } for($i=$from;$i<=$to;$i++) { ?>
			<li class="pagination-item<?php if($pager->current_page == $i) { ?> active<?php } ?>"><a href="<?php echo $prefix.$i.$suffix; ?>"><?php echo $i; ?></a></li>
			<?php } if ($to < $pager->last) { ?>
			<li class="last"><div style="padding:18px 8px 0px 0px;float:left">...</div><a class="button" href="<?php echo $prefix.$pager->last.$suffix; ?>"><?php echo $pager->last; ?></a></li>
			<?php } if ($pager->current_page < $pager->last) { ?>
			<li class="next"><a href="<?php echo $prefix.($pager->current_page+1).$suffix; ?>">Next</a></li>
		<?php } ?>
	</ul>
<?php } ?>
