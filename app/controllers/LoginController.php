<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\SessionHandlerInterface;
class LoginController extends Controller {
	public function __construct(SessionHandlerInterface $session) {
		$this->session = $session;
	}
	function default_action() {
		if ($this->session->loggedIn()) {
			if ($this->session->loggedIn('admin') || $this->session->loggedIn('root')) $this->redirect('admin');
			else $this->redirect('');
		} else {
			$this->render("login");
		}
	}
	function logout() {
		$this->session->destroy();
		$this->redirect("");
	}
	function forgot_password() {
		$this->render("forgot-password");
	}
}
?>
