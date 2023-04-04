<?php

declare(strict_types=1);

namespace Smuuf\Primi\Helpers\Wrappers;

use Smuuf\StrictObject;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\Modules\Importer;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;

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

	/**
	 * @return mixed
	 */
	public function executeBefore() {

		// Detect circular imports.
		if (!$this->importer->pushImport($this->importId)) {
			Exceptions::piggyback(
				StaticExceptionTypes::getImportErrorType(),
				"Circular import when importing: {$this->dotpath}",
			);
		}

	}

	public function executeAfter(): void {
		$this->importer->popImport();
	}

}
