<?php

declare(strict_types=1);

namespace Smuuf\Primi;

use Smuuf\StrictObject;
use Smuuf\Primi\Code\BytecodeProvider;
use Smuuf\Primi\Tasks\TaskQueue;
use Smuuf\Primi\Modules\Importer;

/**
 * Service provider for specific context instances.
 */
class ContextServices {

	use StrictObject;

	private TaskQueue $taskQueue;
	private Importer $importer;
	private BytecodeProvider $astProvider;

	public function __construct(
		private Context $ctx,
		private Config $config,
	) {}

	public function getTaskQueue(): TaskQueue {
		return $this->taskQueue
			??= new TaskQueue($this->ctx);
	}

	public function getImporter(): Importer {
		return $this->importer
			??= new Importer($this->ctx, $this->config->getImportPaths());
	}

	public function getBytecodeProvider(): BytecodeProvider {
		return $this->astProvider
			??= new BytecodeProvider($this->config->getTempDir());
	}

}
