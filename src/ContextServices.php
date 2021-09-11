<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Code\AstProvider;
use \Smuuf\Primi\Tasks\TaskQueue;
use \Smuuf\Primi\Modules\Importer;

/**
 * Service provider for specific context instances.
 */
class ContextServices {

	use StrictObject;

	private Context $ctx;
	private InterpreterServices $interpreterServices;

	private TaskQueue $taskQueue;
	private Importer $importer;

	/** Directory of the main module. */
	private ?string $mainDirectory;

	public function __construct(
		Context $ctx,
		InterpreterServices $interpreterServices,
		?string $mainDirectory = null
	) {
		$this->interpreterServices = $interpreterServices;
		$this->mainDirectory = $mainDirectory;
		$this->ctx = $ctx;
	}

	public function getTaskQueue(): TaskQueue {

		if (isset($this->taskQueue)) {
			return $this->taskQueue;
		}

		return $this->taskQueue = new TaskQueue($this->ctx);

	}

	public function getImporter(): Importer {

		if (isset($this->importer)) {
			return $this->importer;
		}

		// Build import paths list based on interpreter config + the
		// directory of this context's main module, if it is defined.
		$importPaths = $this->interpreterServices
			->getConfig()
			->getImportPaths();

		if ($this->mainDirectory) {
			$importPaths[] = $this->mainDirectory;
		}

		return $this->importer = new Importer($this->ctx, $importPaths);

	}

	//
	// Access to parent interpreter services.
	//

	public function getAstProvider(): AstProvider {
		return $this->interpreterServices->getAstProvider();
	}

}
