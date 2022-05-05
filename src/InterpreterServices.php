<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Code\AstProvider;
use \Smuuf\Primi\Drivers\StdIoDriverInterface;

/**
 * Service provider for specific interpreter instances based on its config.
 */
class InterpreterServices {

	use StrictObject;

	private Config $config;
	private AstProvider $astProvider;

	public function __construct(Config $config) {
		$this->config = $config;
	}

	public function getConfig(): Config {
		return $this->config;
	}

	public function getAstProvider(): AstProvider {

		if (isset($this->astProvider)) {
			return $this->astProvider;
		}

		$tempDir = $this->config->getTempDir();
		return $this->astProvider = new AstProvider($tempDir);

	}

}
