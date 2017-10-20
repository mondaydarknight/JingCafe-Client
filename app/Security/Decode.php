<?php

namespace Security;

class Decode {

	public static function base64($code) {
		return base64_decode($code);
	} 

}