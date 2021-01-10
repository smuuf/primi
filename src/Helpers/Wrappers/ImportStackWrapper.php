<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use \Smuuf\Primi\Ex\CircularImportError;
use \Smuuf\Primi\Helpers\Traits\StrictObject;
use \Smuuf\Primi\Modules\DotPath;
use \Smuuf\Primi\Modules\Importer;

class ImportStackWrapper extends AbstractWrapper {

	use StrictObject;

	/** Importer instance. */
	private Importer $importer;

	/** Import ID for circular import detection. */
	private string $importId;

	/** DotPath object for nice error messages. */
	private DotPath $dotPath;

	public function __construct(
		Importer $importer,
		string $importId,
		DotPath $dotPath
	) {
		$this->importer = $importer;
		$this->importId = $importId;
		$this->dotPath = $dotPath;
	}

	public function executeBefore() {

		// Detect circular imports.
		if (!$this->importer->pushImport($this->importId)) {
			throw new CircularImportError($this->dotPath->getOriginal());
		}

	}

	public function executeAfter() {
		$this->importer->popImport();
	}

}
