<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\Primi\Ex\EngineError;
use \Smuuf\StrictObject;

/**
 * CLI helper class for representing Primi source code - provided either as
 * a path to some file or just a string containing the source.
 */
class Source {

	use StrictObject;

	private string $id;
	private string $code;

	public function __construct(
		string $input,
		bool $isFile,
		?string $id = null
	) {

		if ($isFile) {

			$path = \realpath($input);
			if (!$path || !\is_file($path)) {
				throw new EngineError("File '$path' not found");
			}

			// Hash of the real, absolute path to avoid ambiguity.
			$input = file_get_contents($path);
			$id = sprintf("file %s", $path);

		} else {

			$id = sprintf("string %s", md5($input));

		}

		$this->code = $input;
		$this->id = $id;

	}

	public function getId(): string {
		return $this->id;
	}

	public function getCode(): string {
		return $this->code;
	}

}
