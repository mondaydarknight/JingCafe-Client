<?php

PHPAutoloader::register();

class PHPAutoloader {

	public static function register() {
		if (function_exists('__autoload')) {
			spl_autoload_register('__autoload');
		}

		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			return spl_autoload_register(array('PHPAutoloader', 'load'), true, true);
		} else {
			return spl_autoload_register(array('PHPAutoloader', 'load'));
		}

	}

	public static function load($className) {
		if ((class_exists($className, false))) {
			return false;
		}

		$classFilePath = dirname(__FILE__) . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) .'.php';

		if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
			return false;
		}

		require_once($classFilePath);
	}

}



