<?php

require_once('Autoloader.php');

use Service\Client;
use Service\WebService;

class GetFactory {

	public static function initialization($params) {
		
		$client = new Client;

		$service = new WebService($client);

		$client->request($params);
		
	}

}

GetFactory::initialization($_GET);
