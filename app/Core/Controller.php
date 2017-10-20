<?php

namespace Core;

use Application\ConfigSQL;
use Coffee\Product;

abstract class Controller {

	protected $app;

	protected $user;

	protected $connect;

	// protected $result;

	protected function __construct($app) {
		$this->app = $app;
	}

	protected function userAccessStatus() {
		if (!$this->user->accessProcess()) {
			$this->output('error');
			exit;
		}
	}

	public function viewProduct($params) {

		$this->user->setCategory(mb_strtoupper(mb_substr($params['category'], 0, 1)));
		$this->userAccessStatus();

		$product = new Product($this->user);

		if (isset($params['serial'])) {
			$this->user->setSerial(mb_strtoupper(mb_substr($params['serial'], 0, 1)));
			return $this->output($product->findProductSerial());
		}

		return $this->output($product->findProduct());
	}

	public function viewProductDetail($params) {
		if (!isset($params['serial'])) {
			return $this->output(array('error' => 'URL_INPUT_ERROR'));
		}

		$this->user->setSerial($params['serial']);
		$this->userAccessStatus();

		$product = new Product($this->user);
		$this->output($product->findProductDetail());
	}

	public function output($result) {
		$this->app->sendResponse($result);
	}

}