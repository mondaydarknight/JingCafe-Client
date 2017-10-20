<?php

namespace Service;

class Service implements ServiceInterface {

	private $client;

	private $server;

	public function __construct(Server $server, Client $client) {
		$this->server = $server;
		$this->client = $client;

		$this->server->setService($this);
		$this->client->setService($this);
	}

	public function makeRequest($command) {
		$this->server->process($command);
	}

	public function sendResponse($response) {
		$this->client->responseProcess($response);
	}

}
