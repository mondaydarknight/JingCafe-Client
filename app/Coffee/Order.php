<?php

namespace Coffee;

use Exception;
use Application\Config;
use Application\ConfigSql;
use Application\Process;
use Application\Time;
use Core\DataBaseConnection;
use Core\User;

class Order {

	private $connect;

	private static $transaction = [
		':transactionid' => 0,
		':userid' => 0,
		':username' => '',
		':email' => '',
		':phone' => '',
		':address' =>'',
		':list' => '',
		':deliverid' => 0,
		':bankaccount' => '',
		':totalprice' => 0,
		':message' => ''
	];

	private $orderStatusOutput = [
		' ' => 'orderWaiting',
		'C' => 'orderComplete',
		'D' => 'orderDisable'
	];

	private $orderStatus;

	private $orderSetting;


	public function __construct(User $user) {
		$this->user = $user;
	}

	public function getMemberRecord() {
		return 	'<div class="alert alert-info text-center">'.
				'<label class="checkbox"><input type="checkbox" class="record">同會員資料</label>'.
				'</div>';
	}

	public function view() {
		$this->getConnection();
		if (!$this->searchUserAllOrder()) {
			return array('order' => false);
		}
		
		return $this->viewOrderHtml();
	}

	public function check() {
		$this->getConnection();
		return $this->checkTransactionIsUser();
	}

	public function checkAndListProduct() {
		$this->getConnection();
		if (!$this->checkTransactionIsUser()) {
			return 'error';
		}

		return $this->getOneTransactionOrder();
	}

	public function alter() {
		$this->getConnection();
		if (!$this->checkTransactionIsUser()) {
			return 'error';
		}

		$this->storeTransaction();

		return $this->alterTransaction();
	}

	public function cancel() {
		$this->getConnection();
		if (!$this->cancelTransaction()) {
			return array('error' => '發生錯誤');
		}

		return '取消成功';
	}

	protected function getConnection() {
		$this->connect = new DataBaseConnection(Config::DATABASE_USERNAME_ADMIN);
	}

	/** 
	 * query visitor order
	 * @return boolean
	 */
	protected function searchUserAllOrder() {
		$sql = ConfigSql::SEARCH_USER_ALL_ORDER;
		$factor = [':userId' => $this->user->getUserId()];
		return $this->connect->setSql($sql)->setFactor($factor)->query();
	}

	protected function searchDeliver() {
		return $this->connect->setSql(ConfigSql::SEARCH_DELIVER)->setFactor([])->query();
	}

	protected function checkTransactionIsUser() {
		$sql = ConfigSql::SEARCH_ORDER.' WHERE ispay = :ispay AND userid = :userId AND id = :transactionId AND status != :disable';
		$factor = [
			':ispay' => 0,
			':userId' => $this->user->getUserId(), 
			':transactionId' => $this->user->getTransactionId(),
			':disable' => 'D'
		];
		return $this->connect->setSql($sql)->setFactor($factor)->query();
	}

	protected function alterTransaction() {
		return $this->connect->setSql(ConfigSql::UPDATE_TRANSACTION)->setFactor(self::$transaction)->query() ? '修改成功' : array('error' => '發生錯誤');
	}

	protected function cancelTransaction() {
		$factor = [
			':status' => 'D',
			':userId' => $this->user->getUserId(),
			':transactionId' => $this->user->getTransactionId()
		];
		
		return $this->connect->setSql(ConfigSql::CANCEL_TRANSACTION)->setFactor($factor)->query();
	}

	protected function storeTransaction() {
		self::$transaction[':username'] = $this->user->getUsername();
		self::$transaction[':email'] = $this->user->getEmail();
		self::$transaction[':phone'] = $this->user->getPhone();
		self::$transaction[':bankaccount'] = $this->user->getBankAccount();
		self::$transaction[':list'] = Process::productCombineList($this->user->getProductId(), $this->user->getProductAmount());
		self::$transaction[':deliverid'] = $this->user->getDeliverId();
		self::$transaction[':totalprice'] = $this->user->getPrice();
		self::$transaction[':message'] = $this->user->getMessage();
		self::$transaction[':transactionid'] = $this->user->getTransactionId();
		self::$transaction[':userid'] = $this->user->getUserId();
	}

	protected function getOneTransactionOrder() {
		$order = $this->connect->fetchAssoc();
		$order['user'] = $this->user->getUser();
		$order['username'] = $order['name'];
		$order['record'] = $this->getMemberRecord();
		$order['deliver'] = $this->getDeliverRecord();

		$lists = explode('|', $order['list']);
		$product = [];

		foreach ($lists as $key => $list) {
			$productItem = explode('-', $list);
			$product['productid'] = $productItem[0];
			$product['amount'] = $productItem[1];

			$sql = ConfigSql::SEARCH_PRODUCT.' WHERE id = :proudctId';
			$this->connect->setSql($sql)->setFactor([':proudctId' => $productItem[0]])->query();
			$product = array_merge($product, $this->connect->fetchAssoc());
			$product['serial'] = $product['serialid'];
			$product['totalprice'] = $product['amount'] * $product['price'];

			unset($product['serialid']);
			$order['product'][] = $product;
		}

		unset($order['name']);
		unset($order['list']);

		return $order;
	}

	public function getDeliverRecord() {
		$this->getConnection();
		if (!$this->searchDeliver()) {
			return 'error';
		}

		return $this->listDeliver();
 	}

 	protected function listDeliver() {
 		$deliver = [];
 		
 		foreach ($this->connect->fetchAllAssoc() as $key => $sender) {
 			if ($key === 0) {
 				$deliver[] = 
 					'<div class="alert alert-info radio">'.
 					'<label>'.
 					'<input type="radio" name="deliver-type" data-deliver="'.$sender['id'].'" value="'.$sender['type'].'" checked>'.
 					'<span class="frb-title">'.$sender['name'].'</span>'.
 					'<small class="frb-description">'.$sender['message'].'</small>'.
 					'<span class="pull-right">NT$ <span class="fee">'.$sender['fee'].'</span></span>'.
 					'</label>'.
 					'</div>';
 				continue;
 			}

 			$deliver[] = 
 				'<div class="alert alert-default radio">'.
 				'<label>'.
 				'<input type="radio" name="deliver-type" data-deliver="'.$sender['id'].'" value="'.$sender['type'].'">'.
 				'<span class="frb-title">'.$sender['name'].'</span>'.
 				'<small class="frb-description"><a href="#">請選擇地址</a></small>'.
 				'<span class="pull-right">NT$ <span class="fee">'.$sender['fee'].'</span></span>'.
 				'</label>'.
 				'</div>';
 		}

 		return $deliver;
 	}	

	protected function viewOrderHtml() {
		$orders = [];

		foreach ($this->connect->fetchAllAssoc() as $key => $order) {
			$lists = explode('|', $order['list']);
			
			$productItem = $this->listPackage($lists);
			
			$this->{$this->orderStatusOutput[$order['status']]}($order);

			$order['ispay'] ? '<p class="center text-center">已經付款</p>' : '<p class="center text-center">尚未付款</p>';
			
			$orders[] = '<tr>'.
						'<td><p class="center text-center">'.$order['id'].'</p></td>'.
						'<td><p class="center text-center">'.Time::transferOnlyMonth($order['updatedate']).'</p></td>'.
						'<td>'.$productItem['item'].'</td>'.
						'<td>'.$productItem['price'].'</td>'.
						'<td><p class="center text-center">NT$ '.number_format($order['totalprice']).'</p></td>'.
						'<td>'.$this->orderStatus.'</td>'.
						'<td><p class="center text-center">'.$order['delivername'].'</p></td>'.
						'<td>'.
						'<div class="dropdown text-center center">'.
						'<a class="dropdown-toggle" data-toggle="dropdown" href="#">'.
			            '<i class="fa fa-cog"></i>'.
			            '</a>'.
                		$this->orderSetting.
            			'</div>'.
            			'</td>'.
						'</tr>';
		}

		return array('order' => $orders);
	}

	public function listPackage($lists) {
		$productItem = [
			'item' => '',
			'price' => ''
		];

		foreach ($lists as $key => $list) {
			$productOrder = explode('-', $list);
			
			$sql = ConfigSql::SEARCH_PRODUCT.' WHERE id = :proudctId';
			$query = $this->connect->setSql($sql)->setFactor([':proudctId' => $productOrder[0]])->query();

			$product = $this->connect->fetchAssoc();

			$productItem['item'] .= '<p><a href="/JiCoffee/view/product/detail/?serial='.$product['serialid'].'">'.$product['name'].'x'.$productOrder[1].'</a></p>';
			$productItem['price'] .= '<p class="text-center">NT$ '.$product['price'].'</p>';
		}

		return $productItem;
	}

	public function orderWaiting($order) {
		if (!$order['ispay']) {
			$this->orderStatus = '<p class="center text-center">尚未付款</p>';
			return $this->orderSetting = $this->orderSettingAll($order['id']);
		}

		$this->orderStatus = '<p class="center text-center">訂單成立，待貨中</p>';
		$this->orderSetting = $this->orderSettingOnlyDetail($order['id']);
	}

	public function orderComplete($order) {
		$this->orderStatus = '<p class="center text-center">訂單完成</p>';
		$this->orderSetting = $this->orderSettingOnlyDetail($order['id']);
	}

	public function orderDisable($order) {
		$this->orderStatus = '<p class="center text-center">訂單已取消</p>';
		$this->orderSetting = $this->orderSettingOnlyDetail($order['id']);
	}

	public function orderSettingOnlyDetail($id) {
		return 	'<ul class="dropdown-menu" role="menu" aria-labelledby="set">'.
				'<li><a class="setting" tabindex="-1" data-href="/JiCoffee/view/order/detail/?" data-transactionid="'.$id.'">查看訂單明細</a></li>'.
				'</ul>';
	}

	public function orderSettingAll($id) {
		return 	'<ul class="dropdown-menu" role="menu" aria-labelledby="set">'.
				'<li><a class="setting" tabindex="-1" data-href="/JiCoffee/view/order/detail/?" data-transactionid="'.$id.'">查看訂單明細</a></li>'.
				'<li><a class="setting" tabindex="-1" data-href="/JiCoffee/view/pay/?action=alter&" data-transactionid="'.$id.'">修改訂單</a></li>'.
				'<li><a class="setting" tabindex="-1" data-href="/JiCoffee/view/order/cancel/?" data-transactionid="'.$id.'">取消訂單</a></li>'.
               	'</ul>';
	}

	public function __destruct() {
		unset($this->connect);
	}

}