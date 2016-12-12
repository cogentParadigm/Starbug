<?php
namespace Starbug\Core;
class hook_store_password extends QueryHook {
	public function __construct(SessionHandlerInterface $session) {
		$this->session = $session;
	}
	function validate(&$query, $key, $value, $column, $argument) {
		return (empty($value) ? $value : $this->session->hashPassword($value));
	}
}
?>
