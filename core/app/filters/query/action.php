<?php
	if ((!empty($args['action'])) && (($_SESSION[P("memberships")] & 1) != 1)) {
		$roles = "(permits.role='everyone' || (permits.role='user' && permits.who='".$_SESSION[P('id')]."') || (permits.role='group' && (('".$_SESSION[P('memberships')]."' & permits.who)=permits.who))";
		if ((!empty($args['priv_type'])) && ($args['priv_type'] == "table")) {
			$from = P("permits")." AS permits";
			$permit_type = "permits.priv_type='table'";
		} else {
			$from .= " INNER JOIN ".P("permits")." AS permits";
			$permit_type = "(permits.priv_type='global' || (permits.priv_type='object' && permits.related_id=".$first.".id))"." && ((permits.status & ".$first.".status)=".$first.".status)";
			$roles .= " || (permits.role='owner' && ".$first.".owner='".$_SESSION[P('id')]."') || (permits.role='collective' && ((('".$_SESSION[P('memberships')]."' & ".$first.".collective)>'0') || (('".$_SESSION[P('memberships')]."' & ".$first.".collective)=$first.collective)))";
		}
		$args['where'] = "permits.related_table='".P($first)."'"
		." && permits.action='$args[action]'"
		." && ".$permit_type
		." && ".$roles.")"
		.((empty($args['where'])) ? "" : " && ".$args['where']);
	}
?>