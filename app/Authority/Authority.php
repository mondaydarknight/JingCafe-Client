<?php

namespace Authority;

use Application\Session;
use Core\User;
use Core\DataBaseConnection;

class Authority {

	private $session;

	private $key;

	private $transactionId;

	public function __construct(Session $session) {
		$this->session = $session;
	}

	// public function setKey($key) {
	// 	$this->key = $key;
	// }

	public function setTransactionId($transactionId) {
		$this->transactionId = $transactionId;
	}

	/**
	 * check current server agent is change
	 * @return boolean
	 */

	public function checkUserIsExist() {
		return ($this->session->get('auth')) ? true : false;
	}

	public function checkUserIsValidation() {
		if(!$this->session->get('OBSOLETE') && !$this->session->get('EXPIRES'))
			return false;

		if($this->session->get('EXPIRES') < time())
			return false;

		return true;
	}

	/**
	 * prevent hijacking
	 * @return boolean
	 */
	public function checkUserAgentIsChange() {
		
		if ($this->session->get('auth') !== md5($_SERVER['REMOTE_ADDR']) . $_SERVER['HTTP_USER_AGENT'])
			return false;
		
		// if ($this->session->get('IPaddress') !== $_SERVER['REMOTE_ADDR'])
		// 	return false;
		
		// if ($this->session->get('userAgent') !== $_SESSION['HTTP_USER_AGENT'])
		// 	return false;

		return true;
	}


	public function verifyVisitorTransaction() {
		return ($this->session->get('expire') > time() &&
				$this->session->get('key') && 
				$this->session->get('transactionId') && 
				$this->session->get('key') === $this->key && 
				$this->session->get('transactionId') === $this->transactionId
			) ? true : false;
	}

	// public function clearServerSession() {
	// 	return $this->session->destroy();
	// }
	
}