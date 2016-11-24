<?php
namespace Starbug\Core;
use Starbug\Css\CssLoader;
use Starbug\Core\Routing\RouterInterface;
class Application implements ApplicationInterface {

	protected $controllers;
	protected $models;
	protected $router;
	protected $request;
	protected $response;
	protected $config;
	protected $session;
	protected $css;

	use \Psr\Log\LoggerAwareTrait;

	/**
	 * constructor.
	 */
	public function __construct(
		ControllerFactoryInterface $controllers,
		ModelFactoryInterface $models,
		RouterInterface $router,
		SettingsInterface $settings,
		CSSLoader $css,
		SessionHandlerInterface $session,
		ResponseInterface $response,
		InputFilterInterface $filter
	) {
		$this->controllers = $controllers;
		$this->models = $models;
		$this->router = $router;
		$this->settings = $settings;
		$this->css = $css;
		$this->session = $session;
		$this->response = $response;
		$this->filter = $filter;
	}

	public function handle(RequestInterface $request) {
		$this->session->startSession();
		$permitted = $this->check_post($request->getPost(), $request->getCookies());
		$this->response->assign("request", $request);
		$route = $this->router->route($request);

		if (empty($route['theme'])) $route['theme'] = $this->settings->get("theme");
		if (empty($route['layout'])) $route['layout'] = empty($route['type']) ? "views" : $route['type'];
		if (empty($route['template'])) $route['template'] = $request->getFormat();
		$this->css->setTheme($route['theme']);

		foreach ($route as $k => $v) {
			if (!empty($v)) $this->response->{$k} = $v;
		}
		$this->logger->addInfo("Loading ".$route['controller'].' -> '.$route['action']);
		$controller = $this->controllers->get($route['controller']);

		if (isset($controller->routes[$route['action']])) {
			$template = $controller->routes[$route['action']];
			if (false === ($values = $this->router->validate($request, $route, $template))) {
				$route['action'] = 'missing';
			} else if (is_array($values)) {
				$route['arguments'] = $values;
			}
		}

		if (empty($route['arguments'])) $route['arguments'] = array();

		$controller->start($request, $this->response);
		if ($permitted) $controller->action($route['action'], $route['arguments']);
		else $controller->forbidden();
		$this->response = $controller->finish();
		return $this->response;
	}
	/**
	* run a model action if permitted
	* @param string $key the model name
	* @param string $value the function name
	*/
	protected function post_action($key, $value, $post = array()) {
		$this->logger->addInfo("Attempting action ".$key.' -> '.$value);
		if ($object = $this->models->get($key)) {
			return $object->post($value, $post);
		}
	}

	/**
	* check $_POST['action'] for posted actions and run them through post_act
	*/
	protected function check_post($post, $cookies) {
		if (!empty($post['action']) && is_array($post['action'])) {
			//validate csrf token for authenticated requests
			if ($this->session->loggedIn()) {
				$validated = false;
				if (!empty($cookies['oid']) && !empty($post['oid']) && $cookies['oid'] === $post['oid']) $validated = true;
				if (true !== $validated) {
					return false;
				}
			}
			//execute post actions
			foreach ($post['action'] as $key => $val) return $this->post_action($this->filter->normalize($key), $this->filter->normalize($val), $post[$key]);
		}
		return true;
	}
}
