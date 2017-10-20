<?php

namespace Core;

use PDO;

class DataBaseConnection implements DataBaseInterface {

	protected $connect = null;

	protected $sql = null;

	protected $stmt = null;

	protected $factor = null;

	public function __construct($database) {
		$this->connectDataBase($database);
	}

	protected function connectDataBase($database) {
		$this->connect = DataBase::getInstance()->getConnection($database);
	}

	protected function queryProcess() {
		$this->stmt = $this->connect->prepare($this->sql);
		$this->stmt->execute($this->factor);

		return ($this->stmt->rowCount() > 0) ? true : false;
	}

	public function transaction() {
		// $this->connect->beginTransaction();

		return $this->connect;
	}

	// public function selectDataBase($database) {
	// 	$this->connectDataBase($database);
	// }

	public function setSql($sql) {
		$this->sql = $sql;
		return $this;
	}

	public function setFactor($factor) {
		$this->factor = $factor;
		return $this;
	}

	public function query() {
		return $this->queryProcess();
	}

	public function fetchAssoc() {
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function fetchAllAssoc() {
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetchNum() {
		return $this->stmt->fetch(PDO::FETCH_NUM);
	}

	public function fetchAllNum() {
		return $this->stmt->fetchAll(PDO::FETCH_NUM);
	}

	public function getInsertId($id) {
		return $this->connect->lastInsertId($id);
	}

}

