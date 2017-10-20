<?php

namespace Service;

interface WebAppInterface {

	public function getUser();

	public function getAuthority();

	public function getSession();

}