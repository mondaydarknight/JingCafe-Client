<?php

require_once('Autoloader.php');

use Service\Client;
use Service\WebService;

class PostFactory {

	public static function initialization($params) {
		
		$client = new Client;

		$service = new WebService($client);

		$client->request($params);	
		
	}

}

PostFactory::initialization($_POST);
