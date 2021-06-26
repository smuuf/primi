<?php

namespace Smuuf\Primi\Stdlib\Modules;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Values\AbstractValue;
use \Smuuf\Primi\Extensions\Module;

/**
 * Native '__engine__.importing' module.
 */
return new class extends Module {

	/** Context instance. */
	private Context $ctx;

	public function execute(Context $ctx): array {

		$this->ctx = $ctx;

		return [
			'get_loaded' => [$this, 'get_loaded'],
		];

	}

	/**
	 * _**Only in [CLI](https://w.wiki/QPE)**_.
	 *
	 * Returns memory peak usage used by Primi _(engine behind the scenes)_ in
	 * bytes.
	 */
	public function get_loaded(): AbstractValue {

		$loaded = $this->ctx->getImporter()->getLoaded();

		// We want to return only information about module objects and not paths
		// they were loaded from, so strip that information (paths are keys) in
		// the dict returned from importer instance. Get rid of them.
		return AbstractValue::buildAuto(\array_values($loaded));

	}

};
