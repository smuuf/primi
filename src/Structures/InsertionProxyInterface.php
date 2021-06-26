<?php

declare(strict_types=1);

namespace Smuuf\Primi\Structures;

use \Smuuf\Primi\Values\AbstractValue;

/**
 * Insertion proxy is a special structure that encapsulates a value object which
 * supports insertion. This proxy is used in situations when we already know
 * the KEY under which we want to store something into encapsulated value, but
 * at the same time we don't yet know what VALUE will be stored.
 * At that point an insertion proxy is created with KEY being set and
 * VALUE can be "commited" later.
 *
 * @internal
 * @see \Smuuf\Primi\Handlers\Types\VectorAttr
 * @see \Smuuf\Primi\Handlers\Types\VectorItem
 * @see \Smuuf\Primi\Handlers\Types\Assignment
 */
interface InsertionProxyInterface {

	public function commit(AbstractValue $value): void;

}
