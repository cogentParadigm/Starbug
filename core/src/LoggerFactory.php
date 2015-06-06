<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/LoggerFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
/**
* an implementation of LoggerFactoryInterface
*/
class LoggerFactory implements LoggerFactoryInterface {
	protected $container;
	protected $loggers = array();
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	public function get($logger) {
		if (!isset($this->loggers[$logger])) {
			$this->loggers[$logger] = $this->container->build("Logger");
			$this->loggers[$logger]->set_type($logger);
		}
		return $this->loggers[$logger];
	}
}
