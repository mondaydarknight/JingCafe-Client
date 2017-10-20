<?php

namespace Command;

class Command {

	private $command;

	public function setCommand(CommandInterface $command) {
		$this->command = $command;
	}

	public function runCommand($params) {
		$this->command->$params['operation']($params);
	}

	public function getOutput() {
		return $this->command->getResult();
	}

}