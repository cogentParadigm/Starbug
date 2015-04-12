<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/forms.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup forms
 */
/**
 * @defgroup forms
 * global functions
 * @ingroup global
 */
/**
 * generates a submit button
 * @ingroup forms
 * @param string $label the inner HTML of the button
 * @param star $ops an option string of HTML attributes
 */
function button($label, $ops="") {
	$ops = star($ops);
	efault($ops['type'], "submit");
	$ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ")."btn";
	echo '<button '.html_attributes($ops, false).'>'.$label.'</button>';
}
?>
