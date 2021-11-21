<?php

declare(strict_types=1);

namespace Smuuf\Primi\Code;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\EngineError;

/**
 * Helper class for representing Primi source code provided as path to the
 * source file.
 *
 * @internal
 */
class SourceFile extends Source {

	use StrictObject;

	/**
	 * Directory path of the source file. This is then used to perform relative
	 * imports.
	 */
	private string $directory;

	public function __construct(string $path) {

		if (!\file_exists($path) || !\is_file($path)) {
			throw new EngineError("File '$path' not found");
		}

		$code = \file_get_contents($path);
		$id = \sprintf("<file '%s'>", $path);

		$this->sourceId = $id;
		$this->sourceCode = $code;

		$this->directory = \dirname($path);

	}

	public function getDirectory(): string {
		return $this->directory;
	}

}
