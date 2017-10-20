<?php

namespace Transmit;

use Exception;

class Curl {

	private $curl;

	private $response = false;


	public function __construct($url, $options = array()) {
		$this->curl = curl_init($url);

		foreach ($options as $key => $val) {
			curl_setopt($this->curl, $key, $val);
		}

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
	}

	public function getResponse() {
		if ($this->response) {
			return $this->response;
		}

		$response = curl_exec($this->curl);
		$error = curl_error($this->curl);
		$errno = curl_errno($this->curl);

		if (is_resource($this->curl)) {
			curl_close($this->curl);
		}

		if ($errno !== 0) {
			throw new Exception($error, $errno);
		}

	}

	public function __toString() {
		return $this->getResponse();
	}

	// usage:
	// $curl = new Curl('http://www.domain.com', array(CURLOPT_POSTFIELDS => array('username' => 'user1')));

}
