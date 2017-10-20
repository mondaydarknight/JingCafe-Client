<?php

namespace Core;

// use Exception;
// use Application\Config;
// use Coffee\Product;
// use Coffee\Transaction;
// use Coffee\Order;
// use Filter\Validator;
// use Security\Encode;
// use Security\KeyGenerator;
// use Service\WebAppInterface;

class Visitor extends User {

	// protected $operations = [
	// 	'viewProduct',
	// 	'viewProductDetail',
	// 	'sendOrder',
	// 	'viewOrder',
	// 	'alterOrder',
	// 	'cancelOrder',
	// ];

	public function __construct($user) {
		parent::__construct($user);
	}

	// public function verifyUser($params) {
	// 	$authority = $this->app->getAuthority();
	// 	$authority->setKey($params['visitorKey']);
	// 	$authority->setTransactionId(Validator::validateFloat($params['transactionid']));

	// 	return $this->result = $authority->verifyVisitorTransaction() ? true : array('error' => 'AUTH_ERROR');
	// }

	// public function viewProduct($params) {
	// 	$product = new Product;

	// 	$this->findCategoryExist($params);
		
	// 	$category = Validator::validateChar(mb_strtoupper(mb_substr(trim($params['category']), 0, 1)));

	// 	if (isset($params['serial'])) {
	// 		$serial = Validator::validateChar(mb_strtoupper(mb_substr(trim($params['serial']), 0, 1)));
	// 		return $this->result = $product->findProductSerial($category, $serial);
	// 	}

	// 	$this->result = $product->findProduct($category);
	// }

	// public function viewProductDetail($params) {
	// 	if (!isset($params['serial'])) {
	// 		throw new Exception('Serial Not found');
	// 	}

	// 	$serial = Validator::validateChar(trim($params['serial']));

	// 	if (!Validator::verify()) {
	// 		return $this->result = array('error' => 'INPUT_ERROR');
	// 	}

	// 	$product = new Product;
	// 	$this->result = $product->findProductDetail($serial);
	// }

	// public function viewOrder($params) {
	// 	$this->setPhone($params['phone']);
	// 	$this->setMail($params['email']);
	
	// 	$order = new Order;
	// 	$this->result = $order->addUser($this)->view();
	// }

	// public function storeOrder($params) {
	// 	$transactionId = Validator::validateFloat($params['transactionId']);

	// 	if (!Validator::verify()) {
	// 		return $this->result = array('error' => 'INPUT_ERROR');
	// 	}

	// 	$session = $this->app->getSession();
	// 	$key = $this->generateVisitorKey();
	// 	$session->set('key', $key);
	// 	$session->set('transactionId', $transactionId);
	// 	$session->set('expire', time() + Config::FIVE_MINUTE);

	// 	return $this->result = array('key' => $key, 'transactionId' => $transactionId);
	// }

	// public function alterOrder($params) {

	// }

	// public function cancelOrder($params) {
	// 	$session = $this->app->getSession();
	// 	$order = new Order;

	// 	// $order->addUser($this)->cancel($session);
	// 	$order->cancelVisitorOrder($session);
	// 	$session->destroy();
	// }

	// public function sendOrder($params) {
	// 	$deal = [];

	// 	$deal[':name'] = $params['username'];
	// 	$deal[':email'] = Validator::validateEmail($params['email']);
	// 	$deal[':phone'] = Validator::validateMobile($params['mobile']);
	// 	$deal[':totalprice'] = Validator::validateFloat($params['total']);

	// 	$productName = Validator::validateArrayString($params['productName']);
	// 	$productId = Validator::validateArrayNumber($params['productId']);
	// 	$productAmount = Validator::validateArrayNumber($params['productAmount']);

	// 	if (!Validator::verify()) {
	// 		return $this->result = array('error' => 'INPUT_TYPE');
	// 	}

	// 	$transaction = new Transaction;
	// 	$this->result = $transaction->sendTransaction($deal, $productName, $productId, $productAmount);
	// }

	// public function generateVisitorKey() {
	// 	return Encode::bindHex(KeyGenerator::mcryptGenerate(12));
	// }


	// public function getOutput() {
	// 	return $this->result;
	// }


	// public function __call($operation, $arguments) {
	// 	throw new Exception('No Operation Execute.');
	// }
	
}