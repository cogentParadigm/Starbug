<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/git-hooks.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	$what = array_shift($argv);
	if ("install" == $what) { //INSTALL GIT HOOKS
		$file = fopen(BASE_DIR."/.git/hooks/pre-commit", "wb");
		fwrite(STDOUT, "Writing pre-commit hook...\n");
		fwrite($file, "#!/bin/sh\nsb test\nexit $?");
		fclose($file);
		exec("chmod +x ".BASE_DIR."/.git/hooks/pre-commit");
		$file = fopen(BASE_DIR."/.git/hooks/post-merge", "wb");
		fwrite(STDOUT, "Writing post-merge hook...\n");
		fwrite($file, "#!/bin/sh\nsb migrate\nexit $?");
		fclose($file);
		exec("chmod +x ".BASE_DIR."/.git/hooks/post-merge");
	}
?>
