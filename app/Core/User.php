<?php

namespace Core;

use Filter\Validator;
// use Security\Encryption;

abstract class User {

	protected $user;
	
	protected $username;

	protected $email;

	protected $password;

	protected $phone;

	protected $sex;

	protected $bankAccount;

	protected $category;

	protected $serial;

	protected $transactionId;

	protected function __construct($user) {
		$this->user = $user;
	}

	public function accessProcess() {
		return Validator::verify();
	}

	public function getUser() {
		return $this->user;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setPhone($phone) {
		$this->phone = Validator::validateMobile($phone);
	}

	public function getPhone() {
		return $this->phone;
	}

	public function setEmail($email) {
		$this->email = Validator::validateEmail($email);
	}

	public function getEmail() {
		return $this->email;
	}

	public function setPassword($password) {
		$this->password = $password;
		// $this->password = Encryption::passwordHash($password);
	}

	public function getPassword() {
		return $this->password;
	}

	public function setSex($sex) {
		$this->sex = Validator::validateChar($sex);
	}

	public function getSex() {
		return $this->sex;
	}

	public function setBankAccount($bankAcount) {
		$this->bankAccount = Validator::validateChar($bankAcount);
	}

	public function getBankAccount() {
		return $this->bankAccount;
	}


	public function setCategory($category) {
		$this->category = Validator::validateChar($category);
	}

	public function getCategory() {
		return $this->category;
	}

	public function setSerial($serial) {
		$this->serial = Validator::validateChar($serial);
	}

	public function getSerial() {
		return $this->serial;
	}

	/**
	 * store transactionId
	 * @return float (bigserial)
	 */
	public function setTransactionId($transactionId) {
		$this->transactionId = Validator::validateFloat($transactionId);
	}

	public function getTransactionId() {
		return $this->transactionId;
	}


}	