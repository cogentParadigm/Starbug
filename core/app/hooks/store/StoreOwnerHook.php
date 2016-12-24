<?php
namespace Starbug\Core;
class StoreOwnerHook extends QueryHook {
	public function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, ($this->user->loggedIn() ? $this->user->userinfo("id") : "NULL"));
	}
	function validate(&$query, $key, $value, $column, $argument) {
		return $this->user->loggedIn() ? $this->user->userinfo("id") : "NULL";
	}
}
?>
