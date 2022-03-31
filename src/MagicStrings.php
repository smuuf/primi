<?php

declare(strict_types=1);

namespace Smuuf\Primi;

abstract class MagicStrings {

	public const NATFUN_TAG_FUNCTION = 'primi.function';
	public const NATFUN_TAG_FUNCTIONARG = 'primi.function.arg';
	public const NATFUN_INJECT_CTX = 'inject-context';
	public const NATFUN_NOSTACK = 'no-stack';
	public const NATFUN_CALLCONV = 'call-convention';

	public const TYPE_OBJECT = 'object';

	public const MAGICMETHOD_OP_EQ = '__op_eq__';
	public const MAGICMETHOD_OP_ADD = '__op_add__';
	public const MAGICMETHOD_OP_SUB = '__op_sub__';

}
