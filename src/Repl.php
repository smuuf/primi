<?php

namespace Smuuf\Primi;

class Repl extends \Smuuf\Primi\StrictObject {

	const PROMPT = '>>> ';

	/** @var \Smuuf\Primi\Interpreter **/
	protected $interpreter;

	public function __construct(\Smuuf\Primi\Interpreter $interpreter) {
		$this->interpreter = $interpreter;
	}

	public function start() {

		$i = $this->interpreter;

		while (true) {

			$input = readline(self::PROMPT);

			// Ignore (skip) empty input.
			if ($input == '') {
				continue;
			}

			// Catch a non-command 'exit'.
			if ($input === 'exit') {
				break;
			}

			readline_add_history($input);

			try {

				// Ensure that there's a semicolon at the end.
				// This way users won't have to put it in there themselves.
				$result = $i->run("$input;");
				if ($result instanceof \Smuuf\Primi\Structures\Value) {
					echo $result->getPhpValue() . "\n";
				}

			} catch (\Smuuf\Primi\ErrorException $e) {
				echo($e->getMessage() . "\n");
			}

		}

	}

}
