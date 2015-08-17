<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/HelperFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
/**
* an implementation of HelperFactoryInterface
*/
class HelperFactory implements HelperFactoryInterface {
	protected $container;
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	public function get($helper) {
		return $this->container->build($helper);
	}
}
