<?php

namespace Service;

use Application\Config;
use Command\Command;

class Server extends ServiceBase {

	private $command;

	public function process($request) {
		if ($request['system']) {
			$this->setProcess(Config::CLASS_PATH[$request['system']], $request);
		}
		
		$this->service->sendResponse($this->getProcessResult());
	}

	private function setProcess($process, $request) {
		$this->command = new Command;
		$this->command->setCommand(new $process);
		$this->command->runCommand($request);
	}

	public function getProcessResult() {
		return $this->command->getOutput();
	}

}
