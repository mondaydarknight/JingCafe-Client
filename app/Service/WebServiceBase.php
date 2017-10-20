<?php

namespace Service;

abstract class WebServiceBase {

	protected $service;

	public function setService(WebServiceInterface $service) {
		$this->service = $service;
	}

}