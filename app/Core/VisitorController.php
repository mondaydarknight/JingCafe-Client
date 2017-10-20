<?php

namespace Core;

use Service\WebAppInterface;
use Coffee\Login;

class VisitorController extends Controller implements VisitorInterface {

	public function __construct(WebAppInterface $app) {
		parent::__construct($app);
		$this->user = new Visitor($this->app->getUser());
	}

	public function initialize() {
		$this->output(array('user' => $this->app->getUser()));
	}

	public function login($params) {
		$this->user->setEmail($params['email']);
		$this->user->setPassword($params['password']);

		$login = new Login($this->user);
		$login->setSession($this->app->getSession());
		$this->output($login->signIn());
	}

	public function register($params) {

		$this->user->setUsername($params['username']);
		$this->user->setEmail($params['email']);
		$this->user->setPassword($params['password']);
		$this->user->setSex($params['sex']);
		$this->user->setPhone($params['phone']);

		$this->userAccessStatus();

		$login = new Login($this->user);

		$this->output($login->register());
	}

	public function initTransaction($params) {
		if (count($params) > 1) {
			return $this->output('error');
		}
		$this->output([
			'user' => $this->user->getUser(),
			'box' => '<button class="btn btn-primary btn-sm login" data-toggle="modal" data-target="#login-modal">已經註冊?</button>'
		]);
	}

	public function viewOrder() {
		$this->output('error');
	}

}