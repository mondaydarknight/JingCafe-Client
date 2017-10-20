<?php

namespace Service;

use Exception;
use Application\Config;
use Application\Session;
use Authority\Authority;
use Core\MemberController;
use Core\VisitorController;

class WebService implements WebServiceInterface, WebAppInterface {

	private $client;

	private $session;

	private $authority;

	private $user;

	private $controller;

	public function __construct(Client $client) {
		$this->setSession();
		$this->setAuthority();
		$this->client = $client;
		$this->client->setService($this);
	}

	public function makeRequest($params) {
		$this->identifyUser();

		if (isset($params['authority'])) {
			return $this->sendResponse($this->$params['operation']());
		}

		$this->executeController($params);
	}

	protected function identifyUser() {
		if ($this->authority->checkUserIsExist()) {
			return $this->setUser('Member');
		}

		$this->setUser('Visitor');
	}

	protected function setSession() {
		$this->session = new Session;
	}

	protected function setAuthority() {
		$this->authority = new Authority($this->session);
	}

	protected function setUser($user) {
		$this->user = $user;
	}

	protected function executeController($params) {
		$controller = Config::CONTROLLER[$this->user];
		$this->controller = new $controller($this);
		$this->controller->$params['operation']($params);
		// $this->controller->output();
	}

	public function getSession() {
		return $this->session;
	}

	public function getAuthority() {
		return $this->authority;
	}

	public function getUser() {
		return $this->user;
	}

	public function getResponse() {
		return $this->userController->getOutput();
	}

	public function sendResponse($result) {
		return $this->client->response($result);
	}

	public function __destruct() {
	}

	// public function useVisitorController($params) {
	// 	$this->controller = new VisitorController;
	// 	$this->controller->$params['operation']($params);
	// }

	// protected function serviceFactory($instruction) {
	// 	if (!isset($instruction['system']) || !array_key_exists($instruction['system'], Config::CLASS_PATH)) {
	// 		throw new Exception('Error Request');
	// 	}

	// 	$this->serviceProcess(Config::CLASS_PATH[$instruction['system']], $instruction);
	// }

	// protected function serviceProcess($class, $instruction) {
	// 	$command = new Command;
	// 	$user = $this->getIdentity();

	// 	$command->setCommand(new $class);
	// 	$command->runCommand($instruction);
	// 	$this->sendResponse($command->getOutput());
	// }

}