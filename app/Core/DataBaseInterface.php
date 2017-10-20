<?php

namespace Core;

interface DataBaseInterface {

	/**
     * Choose Database
     *
     * @return void
     */
	// public function selectDataBase($database);

	/**
     * set sql
     *
     * @return void
     */
	public function setSql($sql);

	/**
     * set sql need factor 
     * @return void
     */
	public function setFactor($factor);

	/**
     * execute sql query
     *
     * @return true|false
     */
	public function query();

     /**
     * A SQL Execution of Transaction
     *
     * @return database
     */
     public function transaction();

	public function fetchAssoc();

	public function fetchAllAssoc();

	public function fetchNum();

	public function fetchAllNum();

	public function getInsertId($id);

}