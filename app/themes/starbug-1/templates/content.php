<div id="content" class="<?
	switch ($request->layout) {
		case 'one-column':
			echo 'span-24';
			break;
		case 'two-column-left':
			echo 'span-17';
			break;
		case 'two-column-right':
			echo 'span-17 colborder';
			break;
	}
?>">
	<? render_view(); ?>
</div>
