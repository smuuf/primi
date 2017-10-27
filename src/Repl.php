<?php

namespace Smuuf\Primi;

class Repl extends \Smuuf\Primi\StrictObject {

	const PROMPT = '>>> ';

	/** @var \Smuuf\Primi\Interpreter **/
	protected $interpreter;

	/** @var \Smuuf\Primi\Context **/
	protected $context;

	public function __construct(\Smuuf\Primi\Interpreter $interpreter) {
		$this->interpreter = $interpreter;
	}

	public function start() {

		$i = $this->interpreter;
		$c = $i->getContext();

		while (true) {

			$input = readline(self::PROMPT);
			if ($input == '') {
				continue;
			}

			if ($input === 'exit') {
				break;
			}

			readline_add_history($input);

			try {

				$result = $i->run($input);
				if ($result instanceof \Smuuf\Primi\Structures\Value) {
					echo $result->getPhpValue() . "\n";
				}

			} catch (\Smuuf\Primi\ErrorException $e) {
				echo($e->getMessage() . "\n");
			}

		}

	}

}
