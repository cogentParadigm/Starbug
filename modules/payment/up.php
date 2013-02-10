<?php
	$this->table("payment_gateways",
		"name  type:string  length:255",
		"description  type:text  default:",
		"is_active  type:bool  default:0",
		"is_test_mode  type:bool  default:0"
	);
	$this->table("payment_gateway_settings",
		"payment_gateway_id  type:int  references:payment_gateways id  alias:%name%",
		"name  type:string  length:256",
		"type  type:string  input_type:select  options:text,textarea,select,checkbox,radio,password",
		"options  type:text  default:",
		"test_mode_value  type:text  default:",
		"live_mode_value  type:text  default:",
		"description  type:text  default:"
	);
	//store payment gateways
	$this->store("payment_gateways",
		"name:Authorize.Net  description:Purchase with credit card using Authorize.net"
		//"is_test_mode:1  is_active:1"
	);
	//transaction notifications
	$this->uri("transaction", "template:xhr");
?>
