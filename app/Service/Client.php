<?php

namespace Service;

use Filter\HttpXSSFilter;

class Client extends WebServiceBase {

	private $user;

	private $session;

	public function request($params) {
		$params = $this->filterHttpXSSInjection($params);

		$this->service->makeRequest($params);
	}

	protected function filterHttpXSSInjection($params) {
		$xss = new HttpXSSFilter;

		foreach ($params as $key => $parameter) {
			$params[$key] = $xss->filter($parameter);
		}

		return $params;
	}

	public function response($result) {
		echo json_encode($result);
	}

}
