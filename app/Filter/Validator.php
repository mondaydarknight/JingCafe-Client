<?php

namespace Filter;

use Application\Config;
use Mail\PHPMailer;

class Validator {

	private static $verify = true;

	public static function validateInt($value) {
		return ($value = filter_var($value, FILTER_VALIDATE_INT)) ? $value : self::validateFail();
	}

	public static function validateFloat($value) {
		return ($value = filter_var($value, FILTER_VALIDATE_FLOAT)) ? $value : self::validateFail();
	}

	public static function validateString($value) {
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}

	public static function validateChar($value) {
		return ($value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW)) ? $value : self::validateFail();
	}

	public static function validateBool($value) {
		return ($value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) ? $value : self::validateFail();
	}

	public static function validateMobile($value) {
		return ($value = filter_var($value, FILTER_SANITIZE_NUMBER_INT)) ? $value :self::validateFail();
	}

	public static function validateNumeric($value) {
		return ($value = is_numeric($value)) ? $value : self::validateFail();
	}

	public static function validateArrayString($value) {
		return ($value = filter_var_array($value, FILTER_SANITIZE_STRING)) ? $value : self::validateFail();
	}

	public static function validateArrayNumber($value) {
		return ($value = array_filter($value, Config::CTYPE_DIGIT)) ? $value : self::validateFail();
	}

	public static function validateEmail($value) {
		return PHPMailer::validateAddress($value) ? $value : self::validateFail();
	}

	protected static function validateFail() {
		self::$verify = false;
	}

	public static function verify() {
		return self::$verify;
	}

}
