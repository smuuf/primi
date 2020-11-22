<?php

namespace Smuuf\Primi\Handlers;

use \Smuuf\Primi\Context;
use \Smuuf\Primi\Helpers\Func;
use \Smuuf\Primi\Helpers\SimpleHandler;
use \Smuuf\Primi\Structures\FuncValue;
use \Smuuf\Primi\Structures\FnContainer;

class AnonymousFunction extends SimpleHandler {

	public static function handle(array $node, Context $context) {

		$callId = "<anonymous>() (defined at {$node['_l']}:{$node['_p']})";

		$fn = FnContainer::build(
			$node['body'],
			$node['params'],
			$context->getCurrentScope(),
			$callId
		);

		return new FuncValue($fn);

	}

	public static function reduce(array &$node): void {

		// Prepare list of parameters.
		$params = [];
		if (isset($node['params'])) {
			// Make sure this is always list, even with one item.
			$node['params'] = Func::ensure_indexed($node['params']);
			foreach ($node['params'] as $p) {
				$params[] = $p['text'];
			}
		}

		$node['params'] = $params;

	}

}
