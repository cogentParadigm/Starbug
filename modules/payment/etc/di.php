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
		'currency_locale' => 'en_US.UTF-8',
		'currency_minor_unit' => 2,
		'Starbug\Payment\*Interface' => DI\object('Starbug\Payment\*'),
		'Starbug\Payment\Cart' => DI\object()->constructorParameter('conditions', DI\get('cart_token')),
		'Starbug\Payment\PriceFormatter' => DI\object()
			->constructorParameter('locale', DI\get('currency_locale'))
			->constructorParameter('minorUnit', DI\get('currency_minor_unit'))
);
?>
