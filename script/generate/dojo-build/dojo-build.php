<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/generate/dojo-build/dojo-build.php builds dojo release
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	passthru("cd app/public/js/dojo/util/buildscripts; ./build.sh action=release optimize=shrinksafe layerOptimize=shrinksafe stripConsole=all copyTests=false profileFile=../../../dojo.profile.js cssOptimize=comments");
?>