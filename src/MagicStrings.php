<?php

declare(strict_types=1);

namespace Smuuf\Primi;

abstract class MagicStrings {

	public const MODULE_MAIN_NAME = '__main__';
	public const ATTR_NAME = '__name__';

	public const TYPE_OBJECT = 'object';

	public const MAGICMETHOD_REPR = '__repr__';
	public const MAGICMETHOD_OP_EQ = '__op_eq__';
	public const MAGICMETHOD_OP_ADD = '__op_add__';
	public const MAGICMETHOD_OP_SUB = '__op_sub__';

}
