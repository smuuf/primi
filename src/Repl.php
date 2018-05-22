<?php

declare(strict_types = 1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Structures\Value;
use \Smuuf\Primi\Helpers;
use \Smuuf\Primi\Colors;
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

	/** @var bool**/
	protected $rawOutput = false;

	public function __construct(
		Interpreter $interpreter,
		IReadlineDriver $driver = null,
		$rawOutput = false
	) {

		$this->interpreter = $interpreter;
		$this->rawOutput = $rawOutput;
		$this->driver = $driver ?: new \Smuuf\Primi\Readline;
		$this->historyFilePath = getenv("HOME") . '/' . self::HISTORY_FILE;
		$this->loadHistory();

	}

	public function start() {

		// Allow saving history even on serious errors.
		register_shutdown_function(function() {
			$this->saveHistory();
		});

		$this->loop();

	}

	private function loop() {

		$i = $this->interpreter;

		while (true) {

			$input = $this->driver->readline(self::PROMPT);
			switch (trim($input)) {
				case '':
					// Ignore (skip) empty input.
					continue;
				break;
				case 'exit':
					// Catch a non-command 'exit'.
					break 2;
				break;
			}

			$this->driver->readlineAddHistory($input);

			try {

				// Ensure that there's a semicolon at the end.
				// This way users won't have to put it in there themselves.
				$result = $i->run("$input;");
				if ($result instanceof Value) {
					printf(
						"%s %s\n",
						$result->getStringValue(),
						!$this->rawOutput ? $this->formatType($result) : null
					);
				}

			} catch (\Smuuf\Primi\ErrorException $e) {
				echo($e->getMessage() . "\n");
			}

		}

	}

	private function formatType(Value $value) {

		return Colors::get(sprintf(
			"{darkgrey}(%s %s){_}",
			$value::TYPE,
			Helpers::objectHash($value)
		));

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
