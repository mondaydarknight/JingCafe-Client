<?php

namespace Command;

abstract class Handler {

	private $successor;

	public function setSuccessor($successor) {
		$this->successor = $successor;
	}

	final public function handle() {
		$process = $this->processing();

		if ($process === null) {
			return $this->successor->handle();
		}

		return $process;
	}

	abstract public function processing();
}