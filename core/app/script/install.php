<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file script/install.php installs 3rd party applications and packages
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	$i = array_shift($argv);
	passthru("sh core/app/script/install/$i");
?>
