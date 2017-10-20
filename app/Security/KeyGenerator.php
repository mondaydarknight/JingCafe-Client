<?php

namespace Security;

class KeyGenerator {

	public static function mcryptGenerate($length) {
		return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
	}

	public static function openSSLGenerate($length) {
		return openssl_random_pseudo_bytes($length);
	}

	public static function uniqueGenerate() {
		return md5(uniqid());
	}

}