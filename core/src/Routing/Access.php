<?php
namespace Starbug\Core\Routing;
use Starbug\Core\IdentityInterface;
class Access implements AccessInterface {
	public function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	public function hasAccess($route) {
		if (empty($route["groups"])) return true;
		if (!is_array($route["groups"])) {
			$route["groups"] = [$route["groups"]];
		}
		foreach ($route["groups"] as $group) {
			if ($this->user->loggedIn($group)) return true;
		}
		return false;
	}
}
?>
