<?php

namespace Core;

use Application\Config;
use Filter\Validator;

class Member extends User {
	/**
	 * @return int
	 */
	private $userId;
	/**
	 * @return int
	 */
	private $price;
	/**
	 * @return array string
	 */
	private $productName;
	/**
	 * @return array int
	 */
	private $productId;
	/**
	 * @return character 1
	 */
	private $deliverId;
	/**
	 * @return character 1
	 */
	private $pay;
	/**
	 * @return array
	 */
	private $productAmount;
	/**
	 * @return text
	 */
	private $message;

	public function __construct($user) {
		parent::__construct($user);
	}

	public function setUserId($userId) {
		$this->userId = $userId;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function setPrice($price) {
		$this->price = Validator::validateFloat($price);
	}

	public function getPrice() {
		return $this->price;
	}

	public function setProductName($productName) {
		$this->productName = Validator::validateArrayString($productName);
	}

	public function getProductName() {
		return $this->productName;
	}

	public function setProductId($productId) {
		$this->productId = Validator::validateArrayNumber($productId);
	}

	public function getProductId() {
		return $this->productId;
	}

	public function setProductAmount($productAmount) {
		$this->productAmount = Validator::validateArrayNumber($productAmount);
	}

	public function getProductAmount() {
		return $this->productAmount;
	}

	public function setDeliverId($deliverId) {
		$this->deliverId = Validator::validateChar($deliverId);
	} 

	public function getDeliverId() {
		return $this->deliverId;
	}

	public function setPay($pay) {
		$this->pay = $pay;
	}

	public function getPay() {
		return $this->pay;
	}

	public function setMessage($message) {
		$this->message = Validator::validateString($message);
	}

	public function getMessage() {
		return $this->message;
	}

}