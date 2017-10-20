<?php

namespace Core;

use PDO;
use Exception;
use Application\Config;

class DataBase {

	/*
	 * DataBase instance  (use Singleton)
	 */
	private static $instance = null;

	/*
	 * return database 
	 */
	private $activeConnection = null;

	protected static $connection = array(
		'admin' => array(
			'dsn' => 'pgsql:host='.Config::HOST.';dbname='.Config::DATABASE_CAFE.';port='.Config::DATABASE_PORT,
			'username' => Config::DATABASE_USERNAME_ADMIN,
			'password' => Config::DATABASE_PASSWORD_ADMIN
		)
	);

	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new DataBase;
		}
		return self::$instance;
	}

	public function getConnection($dbname) {
		if (!isset(self::$connection[$dbname])) {
			throw new Exception('Connection "' . $dbname . '" is not configured.');
		}

		return $this->activeConnection = new PDO(self::$connection[$dbname]['dsn'], self::$connection[$dbname]['username'], self::$connection[$dbname]['password']);

		// return $this->activeConnection;
	}

	public function __destruct() {
		unset($this->activeConnection);
	}

}