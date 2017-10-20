<?php

require_once('Autoloader.php');

use Service\Client;
use Service\WebService;

class Request {

	public static function initialization($params) {
		
		$client = new Client;

		$service = new WebService($client);

		$client->request($params);
		
	}
}

Request::initialization($_REQUEST);
