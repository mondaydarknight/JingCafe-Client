<?php

namespace Coffee;

use Exception;
use Application\ConfigSql;
use Application\Time;
use Application\Session;
use Core\User;
use Core\DataBaseConnection;
use Security\Encryption;

class Login {

	private $user;

	private $connect;

	private $session;

	private $member;

	public function __construct(User $user) {
		$this->user = $user;
		$this->getConnection();
	}

	public function setSession(Session $session) {
		$this->session = $session;
	}

	public function getConnection() {
		$this->connect = new DataBaseConnection('admin');
	}

	public function signIn() {
		if (!$this->checkUserAccount()) {
			return 'accountFail';
		}

		$this->member = $this->connect->fetchAssoc();

		if (!$this->checkUserPassword()) {
			return 'passwordFail';
		}

		if (!$this->checkIsIncognito()) {
			return 'detectDeviceFail';
		}

		return $this->setUserAccessAuthority();
	}

	private function checkUserAccount() {
		return $this->connect->setSql(ConfigSql::SEARCH_EMAIL_EXIST)->setFactor([':email' => $this->user->getEmail()])->query();
	}

	private function checkUserPassword() {
		return password_verify($this->user->getPassword(), $this->member['password']); 
	}

	private function checkIsIncognito() {
		return isset($_SERVER['HTTP_USER_AGENT']);
	}

	protected function setUserAccessAuthority() {
		$this->session->set('auth', md5($_SERVER['REMOTE_ADDR']) . $_SERVER['HTTP_USER_AGENT']);
		$this->session->set('userId', $this->member['id']);
		$this->session->set('email', $this->user->getEmail());
		$this->session->set('username', $this->member['username']);
		$this->session->set('phone', $this->member['phone']);
		return 'success';
	}

	public function register() {
		if ($this->connect->setSql(ConfigSql::SEARCH_EMAIL_EXIST)
			->setFactor([':email' => $this->user->getEmail()])->query()) {
			return array('warning' => 'email');
		}

		$member = [];
		$member[':username'] = $this->user->getUsername();
		$member[':email'] = $this->user->getEmail();
		$member[':password'] = Encryption::passwordHash($this->user->getPassword());
		$member[':phone'] = $this->user->getPhone();
		$member[':sex'] = $this->user->getSex();
		$member[':createdate'] = Time::getCurrentTimeOnlyNumber();

		return $this->connect->setSql(ConfigSql::INSERT_MEMBER)->setFactor($member)->query() ? ['process' => true, 'email' => $this->user->getEmail(), 'password' => $this->user->getPassword()] : ['error' => 'SQL_ERROR'];

	}

}