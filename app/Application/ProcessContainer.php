<?php

namespace Application;

use Exception;

class ProcessContainer {

	protected $container = null;

	public function listen($id, $process) {
		$this->container = array($id => $process);
	}

	public function trigger($id, $result = null) {
		if (!isset($this->container[$id])) {
			throw new Exception("No Process Loaded");
		}

		foreach ($this->container as $key => $process) {
			if ($key === $id) {
				$process::execute($result);	
			}
		}
	}

	public function remove() {

	}

}



