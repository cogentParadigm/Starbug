<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/console.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup console
 */
/**
 * @defgroup console
 * logging utility
 * @ingroup lib
 */
$sb->provide("core/lib/console");
/**
 * allows errors and notifications to be logged and traced
 * @ingroup console
 */
class console {
	/**
	 * @var array logs
	 */
	var $logs = array();
	/**
	 * @var int count of logs
	 */
	var $count;
	/**
	 * @var int maximum number of logs. 0 for no maximum.
	 */
	var $max = 1000;


	/**
	 * save a message.
	 * @param string $message the message
	 * @param string $type type of message such as 'success', 'info', 'notice', 'alert', 'error' or any custom message type you wish to employ.
	 * @param string $tags tag names such as 'global', 'db' or 'module'
	 * @see getLogs
	 */
	public function log($message, $type='info', $tags='global') {
		$this->logs[] = array("tags" => array_merge(array($type), explode(",", $tags)), "time" => microtime(true), "message" => $message);
		$this->count++;
		if($this->max > 0 && $this->count>=$this->max) $this->clear();
	}

	/**
	 * retrieve logs.
	 * @param string $filters comma delimited list of filters, both types and tags can be specified. eg. 'info,notice,module'
	 * @param bool $inclusive if true, results will be return if any filters match. if false, results will be returned where ALL filters match. false by default.
	 * @return array of messages in the form:
	 * array(
	 *   [message] => string
	 *   [tags] => array
	 *   [time] => float
	 * )
	 */
	public function logs($filters, $inclusive=false) {
		$filters = explode(",", $filters);
		$logs = $this->logs;
		if (empty($filters)) return $logs;
		else {
			$count = count($filters);
			foreach ($logs as $idx => $log) {
				$intersect = count(array_intersect($log['tags'], $filters));
				if (($inclusive && empty($intersect)) || (!$inclusive && $intersect < $filter_count)) unset($logs[$idx]);
			}
			return array_values($logs);
		}
	}

	/**
	 * clear logs
	 */
	public function clear() {
		$this->logs = array();
		$this->count = 0;
	}
}
