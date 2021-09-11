<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use \Smuuf\StrictObject;
use \Smuuf\Primi\Ex\CircularImportError;
use \Smuuf\Primi\Modules\Importer;

class ImportStackWrapper extends AbstractWrapper {

	use StrictObject;

	/** Importer instance. */
	private Importer $importer;

	/** Import ID for circular import detection. */
	private string $importId;

	/** Dotpath as string for pretty error messages. */
	private string $dotpath;

	public function __construct(
		Importer $importer,
		string $importId,
		string $dotpath
	) {
		$this->importer = $importer;
		$this->importId = $importId;
		$this->dotpath = $dotpath;
	}

	public function executeBefore() {

		// Detect circular imports.
		if (!$this->importer->pushImport($this->importId)) {
			throw new CircularImportError($this->dotpath);
		}

	}

	public function executeAfter() {
		$this->importer->popImport();
	}

}
