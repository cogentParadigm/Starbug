<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/MailerInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */

interface MailerInterface {

	/**
	 * send an email email
	 * @param array $options
	 * @param array $data
	 */
	function send($options = array(), $data = array(), $rendered=false);
}
