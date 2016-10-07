<?php
use \Interop\Container\ContainerInterface;
return array(
	'cart_token' => function(ContainerInterface $c) {
			$request = $c->get("Starbug\Core\RequestInterface");
			$url = $c->get("Starbug\Core\URLInterface");
			if ($cid = $request->getCookie("cid")) {
			} else {
				$cid = md5(uniqid(mt_rand(), true));
				setcookie("cid", $cid, 0, $url->build(""), null, false, false);
				$request->setCookie("cid", $cid);
			}
			return ["token" => $cid];
		},
		'Starbug\Payment\*Interface' => DI\object('Starbug\Payment\*'),
		'Starbug\Payment\Cart' => DI\object()->constructorParameter('conditions', DI\get('cart_token'))
);
?>
