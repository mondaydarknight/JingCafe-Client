<?php

namespace Service;

interface WebServiceInterface {

	public function makeRequest($params);

	public function sendResponse($result);
}