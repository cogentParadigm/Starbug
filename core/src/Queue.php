<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/QueueInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * a simple interface for a queue
 */
class Queue implements QueueInterface {
	public $name;
	protected $items = array();
	protected $db;
	function __construct(DatabaseInterface $db, $name) {
		$this->db = db;
		$this->name = $name;
	}
	function put($item) {
		$item['queue'] = $this->name;
		if (empty($item['status'])) $item['status'] = "pending";
		if (is_array($item['data'])) $item['data'] = json_encode($item['data']);
		$this->db->store("queues", $item);
	}
	function get() {
		$item = $this->db->query("queues")->condition("queue", $this->name)->condition("status", "pending")->sort("position")->one();
		$item['data'] = json_decode($item['data'], true);
		return $item;
	}
	function release($item) {
		$this->db->query("queues")->condition("id", $item['id'])->set("status", "pending")->update();
	}
	function remove($item) {
		$this->db->query("queues")->condition("id", $item['id'])->delete();
	}
	function success($item, $status = "completed") {
		$this->db->query("queues")->condition("id", $item['id'])->set("status", $status)->update();
	}
	function failure($item, $message="", $status = "failed") {
		$this->db->query("queues")->condition("id", $item['id'])->set("message", $message)->set("status", $status)->update();
	}
	function load() {
		$this->items = $this->db->query("queues")->condition("queue", $this->name)->sort("position")->all();
	}
	function clear() {
		$this->db->query("queues")->condition("queue", $this->name)->delete();
		$this->items = array();
	}
	function count() {
		return $this->db->query("queues")->condition("queue", $this->name)->condition("status", "pending")->count();
	}
}
