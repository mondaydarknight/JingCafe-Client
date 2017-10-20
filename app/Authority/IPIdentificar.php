<?php

namespace Authority;

use Command\Handler;

class IPIdentificar extends Handler {

	private static $ipKeys = array(
		'HTTP_CLIENT_IP', 
		'HTTP_X_FORWARDED_FOR', 
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED_FOR', 
		'HTTP_FORWARDED', 
		'REMOTE_ADDR',
		'HTTP_X_CLUSTER_CLIENT_IP'
	);

	private static $ipRestrict = array(
		'10.1.1.', 
		'127.0.0.', 
		'172.18.1.'
	);

	protected function getServerIP() {
		foreach (self::$ipKeys as $key) {
			if (isset($_SERVER[$key])) {
				return $_SERVER[$key];		
			}
		}

		return false;
	}

	protected function validatePublicIP($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ? $ip : false;
	} 

	protected function getPermission() {
		foreach (self::$ipRestrict as $ip) {
			if (strpos($this->getHostName(), $ip) !== false) {
				return true;
			}
		}

		return null;
	}

	public function getHostName() {
		return $this->getServerIP();
	}

	public function processing() {
		return $this->getPermission();
	}

}
