<?php

namespace Smuuf\Primi;

use \Smuuf\Primi\Interpreter;
use \Smuuf\Primi\IReadlineDriver;

class Repl extends \Smuuf\Primi\StrictObject {

	const HISTORY_FILE = '.primi_history';
	const PROMPT = '>>> ';

	/** @var string Full path to readline history file. **/
	private $historyFilePath;

	/** @var \Smuuf\Primi\Interpreter **/
	protected $interpreter;

	/** @var \Smuuf\Primi\IReadlineDriver **/
	protected $driver;

	public function __construct(Interpreter $interpreter, IReadlineDriver $driver = null) {

		$this->interpreter = $interpreter;
		$this->driver = $driver ?: new \Smuuf\Primi\Readline;
		$this->historyFilePath = getenv("HOME") . '/' . self::HISTORY_FILE;
		$this->loadHistory();

	}

	public function start() {

		$i = $this->interpreter;

		while (true) {

			$input = $this->driver->readline(self::PROMPT);

			// Ignore (skip) empty input.
			if ($input == '') {
				continue;
			}

			// Catch a non-command 'exit'.
			if ($input === 'exit') {
				break;
			}

			$this->driver->readlineAddHistory($input);

			try {

				// Ensure that there's a semicolon at the end.
				// This way users won't have to put it in there themselves.
				$result = $i->run("$input;");
				if ($result instanceof \Smuuf\Primi\Structures\Value) {
					echo $result->getStringValue() . "\n";
				}

			} catch (\Smuuf\Primi\ErrorException $e) {
				echo($e->getMessage() . "\n");
			}

		}

		$this->saveHistory();

	}

	private function loadHistory() {

		if (is_readable($this->historyFilePath)) {
			$this->driver->readlineReadHistory($this->historyFilePath);
		}

	}

	private function saveHistory() {

		if (is_writable(dirname($this->historyFilePath))) {
			$this->driver->readlineWriteHistory($this->historyFilePath);
		}

	}

}
