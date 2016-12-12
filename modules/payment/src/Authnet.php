<?php
// @codingStandardsIgnoreFile
namespace Starbug\Payment;
use Starbug\Core\TemplateInterface;
use SimpleXMLElement;
class AuthnetXMLException extends \Exception {
	//generic exception class to allow instanceof matches
}
/**
 * The Authnet class. A simple wrapper for the Authorize.Net XML APIs
 * API documentation available at http://developer.authorize.net/api/
 * @ingroup payment
 */
class Authnet {

	public $url;
	public $login_id;
	public $transaction_key;
	public $test_mode;
	public $ch;
	public $xml;
	public $response;

	/**
	 * constructor. initializes the Authnet object capable of connecting to AIM, ARB or CIM
	 * Login ID and Transaction Key will be fetched from payment gateway settings
	 */
	public function __construct(PaymentSettingsInterface $settings, TemplateInterface $templates) {
		$this->login_id        = $settings->get('Authorize.Net', 'login_id');
		$this->transaction_key = $settings->get('Authorize.Net', 'transaction_key');
		$this->test_mode       = $settings->testMode("Authorize.Net");
		$this->url = 'https://'.($this->test_mode ? "apitest" : "api").'.authorize.net/xml/v1/request.api';
		$this->templates = $templates;
	}

	/**
	 * generic API function caller.
	 * example:
	 * $authnet = new Authnet();
	 * $params = array(...ARB params..);
	 * $authnet->ARBCreateSubscriptionRequest($params);
	 *
	 * such a call will build the XML request from templates/Authnet/ARBCreateSubscriptionRequest.php within this module
	 */
	public function __call($api_call, $args) {
		foreach ($args[0] as $key => $value) if (!empty($value)) $this->{$key} = $value;
		$this->xml = $this->templates->get("Authnet/$api_call", array("authnet" => $this));
		$this->process();
	}

	/**
	 * private function used to submit API call via CURL and fetch results
	 */
	private function process() {
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
		curl_setopt($this->ch, CURLOPT_HEADER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->xml);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		$this->response = curl_exec($this->ch);
		if ($this->response != false) {
			libxml_use_internal_errors(true);
			$this->response = end(explode('<?xml version="1.0" encoding="utf-8"?>', $this->response));
			$this->response = new SimpleXMLElement($this->response);
			$this->refId = $this->response->refId;
			$this->resultCode = $this->response->messages->resultCode;
			$this->code = $this->response->messages->message->code;
			$this->text = $this->response->messages->message->text;
			if (isset($this->response->subscriptionId)) $this->subscriptionId = $this->response->subscriptionId;
		}
		curl_close($this->ch);
	}

	/**
	 * check if an API call was successful
	 * @return bool true if successful, false otherwise
	 */
	public function success() {
		return $this->resultCode == 'Ok';
	}

	/**
	 * check if an API has failed
	 * @return bool true if not successful, false otherwise
	 */
	public function error() {
		return $this->resultCode != 'Ok';
	}
}

?>
