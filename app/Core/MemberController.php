<?php

namespace Core;

use Exception;
use Coffee\Transaction;
use Coffee\Order;
use Coffee\Menu;
use Service\WebAppInterface;

class MemberController extends Controller implements MemberInterface {

	public function __construct(WebAppInterface $app) {
		parent::__construct($app);
		$this->user = new Member($this->app->getUser());
		$this->userVerify();
	}

	private function userVerify() {
		if (!$this->app->getAuthority()->checkUserAgentIsChange()) {
			echo 'userChange';
			exit;
		}
	}

	public function initialize() {
		$result = [];
		$this->user->setUsername($this->app->getSession()->get('username'));
		$this->user->setEmail($this->app->getSession()->get('email'));
		$menu = new Menu($this->user);

		$result['user'] = $this->app->getUser();
		$result['orderMenu'] = $menu->navbarOrder();
		$result['userMenu'] = $menu->navbarUser();


		$this->output($result);
	}

	public function logout() {
		$this->app->getSession()->destroy();
		$this->output(true);
	}

	public function initTransaction($params) {
		if (isset($params['action']) && $params['action'] === 'alter' && isset($params['transactionid'])) {
			$this->user->setUserId($this->app->getSession()->get('userId'));
			$this->user->setTransactionId($params['transactionid']);
			$order = new Order($this->user);
			return $this->output($order->checkAndListProduct());
		}

		$order = new Order($this->user);
		$this->output([
			'user' => $this->user->getUser(),
			'record' => $order->getMemberRecord(),
			'deliver' => $order->getDeliverRecord(),
			'username' => $this->app->getSession()->get('username'),
			'email' => $this->app->getSession()->get('email'),
			'phone' => $this->app->getSession()->get('phone'),
			'bankaccount' => $this->app->getSession()->get('bankaccount')
		]);
	}

	public function checkTransaction($params) {
		$this->user->setTransactionId($params['transactionid']);
		$this->user->setEmail($this->app->getSession()->get('email'));
		$this->user->setUserId($this->app->getSession()->get('userId'));
		$this->userAccessStatus();

		$order = new Order($this->user);
		$this->output($order->check() ? true : false);
	}

	public function sendOrder($params) {
		$this->user->setUserId($this->app->getSession()->get('userId'));
		$this->user->setUsername($params['username']);
		$this->user->setEmail($params['email']);
		$this->user->setPhone($params['phone']);
		$this->user->setPrice($params['total']);
		$this->user->setDeliverId($params['deliverId']);
		$this->user->setBankAccount($params['bankAccount']);
		$this->user->setMessage($params['message']);
		$this->user->setProductName($params['productName']);
		$this->user->setProductId($params['productId']);
		$this->user->setProductAmount($params['productAmount']);
		$this->userAccessStatus();

		$transaction = new Transaction($this->user);
		$this->output($transaction->sendTransaction());
	}

	public function viewOrder($params) {
		$this->user->setUserId($this->app->getSession()->get('userId'));
		$this->user->setEmail($this->app->getSession()->get('email'));
		$order = new Order($this->user);
		$this->output($order->view());
	}

	public function storeOrder($params) {
		$transactionId = Validator::validateFloat($params['transactionId']);

		if (!Validator::verify()) {
			return $this->result = array('error' => 'INPUT_ERROR');
		}

		$session = $this->app->getSession();
		$key = $this->generateVisitorKey();
		$session->set('key', $key);
		$session->set('transactionId', $transactionId);
		$session->set('expire', time() + Config::FIVE_MINUTE);

		return $this->result = array('key' => $key, 'transactionId' => $transactionId);
	}

	public function alterOrder($params) {
		$this->user->setTransactionId($params['transactionid']);
		$this->user->setUserId($this->app->getSession()->get('userId'));
		$this->user->setUsername($params['username']);
		$this->user->setEmail($params['email']);
		$this->user->setPhone($params['phone']);
		$this->user->setBankAccount($params['bankAccount']);
		$this->user->setDeliverId($params['deliverId']);
		$this->user->setPrice($params['total']);
		$this->user->setMessage($params['message']);
		$this->user->setProductName($params['productName']);
		$this->user->setProductId($params['productId']);
		$this->user->setProductAmount($params['productAmount']);
		$this->userAccessStatus();

		$order = new Order($this->user);
		$this->output($order->alter());

	}

	public function cancelOrder($params) {
		$this->user->setUserId($this->app->getSession()->get('userId'));
		$this->user->setTransactionId($params['transactionId']);
		
		$order = new Order($this->user);
		$this->output($order->cancel());
	}


	public function __call($operation, $arguments) {
		throw new Exception('No Operation Execute.');
	}
	
}