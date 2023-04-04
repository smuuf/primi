<?php

declare(strict_types=1);

namespace Smuuf\Primi\VM;

use Smuuf\StrictObject;
use Smuuf\Primi\Context;
use Smuuf\Primi\Ex\EngineError;
use Smuuf\Primi\Ex\EngineInternalError;
use Smuuf\Primi\Code\BytecodeDumper;
use Smuuf\Primi\Ex\PiggybackException;
use Smuuf\Primi\Values\AbstractValue;
use Smuuf\Primi\Values\DictValue;
use Smuuf\Primi\Values\ListValue;
use Smuuf\Primi\Values\TupleValue;
use Smuuf\Primi\Stdlib\StaticExceptionTypes;
use Smuuf\Primi\Helpers\Func;
use Smuuf\Primi\Helpers\Types;
use Smuuf\Primi\Helpers\Interned;
use Smuuf\Primi\Helpers\Iteration;
use Smuuf\Primi\Handlers\Kinds\AttrAccess;
use Smuuf\Primi\Handlers\Kinds\ClassDefinition;
use Smuuf\Primi\Handlers\Kinds\Comparison;
use Smuuf\Primi\Handlers\Kinds\Dereference;
use Smuuf\Primi\Handlers\Kinds\FunctionDefinition;
use Smuuf\Primi\Handlers\Kinds\ImportStatement;
use Smuuf\Primi\Handlers\Kinds\Variable;
use Smuuf\Primi\Helpers\Exceptions;
use Smuuf\Primi\ScopeComposite;
use Smuuf\Primi\Structures\CallArgs;

class Machine {

	use StrictObject;

	//
	// List of known ops.
	//
	// NOTE: These being here and as constants is _very_ intentional.
	// Having these somewhere else (as enum or even some other class with
	// constants) leads (at least for PHP 8.1) to massively slower VM.
	// Beware!
	//

	public const OP_NOOP = 'NOOP';
	public const OP_LOAD_CONST = 'LOAD_CONST';
	public const OP_POP = 'POP';
	public const OP_DUP_TOP = 'DUP_TOP';
	public const OP_SWAP = 'SWAP';
	public const OP_COPY = 'COPY';
	public const OP_RETURN = 'RETURN';
	public const OP_JUMP = 'JUMP';
	public const OP_JUMP_IF_T = 'JUMP_IF_T';
	public const OP_JUMP_IF_F = 'JUMP_IF_F';
	public const OP_JUMP_IF_T_OR_POP = 'JUMP_IF_T_OR_POP';
	public const OP_JUMP_IF_F_OR_POP = 'JUMP_IF_F_OR_POP';
	public const OP_LABEL = 'LABEL';
	public const OP_ADD = 'ADD';
	public const OP_SUB = 'SUB';
	public const OP_MULTI = 'MULTI';
	public const OP_DIV = 'DIV';
	public const OP_EXP = 'EXP';
	public const OP_NEGATE = 'NEGATE';
	public const OP_CAST_BOOL = 'CAST_BOOL';
	public const OP_LOAD_NAME = 'LOAD_NAME';
	public const OP_LOAD_ATTR = 'LOAD_ATTR';
	public const OP_LOAD_ITEM = 'LOAD_ITEM';
	public const OP_STORE_NAME = 'STORE_NAME';
	public const OP_STORE_ATTR = 'STORE_ATTR';
	public const OP_STORE_ITEM = 'STORE_ITEM';
	public const OP_BUILD_LIST = 'BUILD_LIST';
	public const OP_BUILD_DICT = 'BUILD_DICT';
	public const OP_BUILD_CONST_LIST = 'BUILD_CONST_LIST';
	public const OP_BUILD_CONST_DICT = 'BUILD_CONST_DICT';
	public const OP_BUILD_TUPLE = 'BUILD_TUPLE';
	public const OP_BUILD_STRING = 'BUILD_STRING';
	public const OP_LIST_EXTEND = 'LIST_EXTEND';
	public const OP_LIST_APPEND = 'LIST_APPEND';
	public const OP_DICT_MERGE = 'DICT_MERGE';
	public const OP_DICT_SET_ITEM = 'DICT_SET_ITEM';
	public const OP_CALL_FUNCTION = 'CALL_FUNCTION';
	public const OP_CALL_FUNCTION_N = 'CALL_FUNCTION_N';
	public const OP_CALL_FUNCTION_EX = 'CALL_FUNCTION_EX';
	public const OP_UNPACK_ITERABLE = 'UNPACK_ITERABLE';
	public const OP_UNPACK_MAPPING = 'UNPACK_MAPPING';
	public const OP_IMPORT = 'IMPORT';
	public const OP_COMPARE_EQ = 'COMPARE_EQ';
	public const OP_COMPARE_NEQ = 'COMPARE_NEQ';
	public const OP_COMPARE_IN = 'COMPARE_IN';
	public const OP_COMPARE_RELATION = 'COMPARE_RELATION';
	public const OP_ITER_GET = 'ITER_GET';
	public const OP_ITER_NEXT = 'ITER_NEXT';
	public const OP_UNPACK_SEQUENCE = 'UNPACK_SEQUENCE';
	public const OP_CREATE_FUNCTION = 'CREATE_FUNCTION';
	public const OP_TRYBLK_PUSH = 'TRYBLK_PUSH';
	public const OP_TRYBLK_POP = 'TRYBLK_POP';
	public const OP_EXC_MATCH = 'EXC_MATCH';
	public const OP_EXC_THROW = 'EXC_THROW';
	public const OP_CREATE_CLASS = 'CREATE_CLASS';

	private int $callStackLimit;

	public function __construct(
		private Context $ctx,
	) {
		$this->callStackLimit = $ctx->getConfig()->getCallStackLimit();;
	}

	public function run(Frame $frame): AbstractValue {

		$ctx = $this->ctx;
		$ctx->setCurrentFrame($frame);

		if ($frame->callStackSize > $this->callStackLimit) {
			throw new EngineError(\sprintf(
				"Maximum call stack size (%d) reached",
				$this->callStackLimit,
			));
		}

		$builtins = $ctx->getBuiltins();
		$scope = $frame->getScope();
		$scopeComp = new ScopeComposite($scope, $ctx->getBuiltins());

		$bytecode = $frame->getBytecode();
		$opIndex = -1;
		$vStack = $frame->getValueStack();
		$maxIndex = $bytecode->length - 1;
		$ops = $bytecode->ops;

		// Setup try-block for catching PiggybackExceptions.
		try {

			// To avoid PHP giving us the "'goto' into loop or switch statement
			// is disallowed" error we need to do (order) some of the stuff in a
			// seemingly weird way. For example we have the "vm_check_exc"
			// outside of the main VM loop + the exception check/handling
			// is inside. And the main loop doesn't even jump back to the
			// main "while(true)", but we jump manually to the "vm_do_inc_index"
			// label after the big ol' switch. That way we avoid checking
			// exceptions after every instruction, but we can jump there any
			// time we need to actually check exceptions (for example after
			// functions calls, etc.).
			vm_check_exc:

			while (\true) {

				//
				// Primi exceptions handling.
				//

				if ($exc = $ctx->getPendingException()) {

					// If the traceback has not been set yet, do it now.
					if (!$exc->getTraceback()) {
						$exc->setTraceback(Exceptions::unwindStack($ctx));
					}

					// Look for the catch index inside current frame.
					[$catchIndex, $oss, $varName] = $frame->findCatch($exc);
					if ($catchIndex !== \null) {

						// We were able to catch the exception within a current
						// try-block within the current frame - go to the op that
						// handles the catch.
						$ctx->getAndResetException();
						$opIndex = $catchIndex;

						// Store the exception object into scope as variable.
						// This handles "... catch (Exception [as varName]) ..."
						if ($varName) {
							$scope->setVariable($varName, $exc->exception);
						}

						// Pop (original_stack_size - current_stack_size) items
						// from the value stack - making sure we don't leave any
						// extra values on value stack (values that may be left
						// on the value stack at the time of the exception being
						// thrown).
						$vStack->popN(\count($vStack) - $oss);
						goto vm_do_index;

					}

					// We were not able to catch the current exception within
					// the current try-block, so we need to traverse up the call
					// stack to try to find the appropriate try-block.
					// Clear the value stack first.
					$vStack->clear();
					goto vm_loop_exit;

				}

				vm_do_inc_index:

				$opIndex++;
				if ($opIndex > $maxIndex) {
					throw new EngineInternalError("VM op index overflow");
				}

				vm_do_index:
				$op = $ops[$opIndex];

				if (\false) {

					//usleep(50_000);
					$args = BytecodeDumper::formatArgs(array_reverse(iterator_to_array($vStack)));
					$suffix = "{$vStack->count()}] $args";
					BytecodeDumper::dumpOp(
						op: $op,
						index:
						$opIndex,
						opLocData: $bytecode->linesInfo,
						suffix: $suffix
					);

				}

				//
				// Main opcode switch.
				//

				switch ($op[0]) {
					case Machine::OP_LOAD_CONST:
						// Arg #1: Value.
						$vStack->push($op[1]);
						break;

					case Machine::OP_LOAD_NAME:
						// Arg #1: Variable name.
						$vStack->push(Variable::fetch($op[1], $scopeComp));
						break;

					case Machine::OP_LOAD_ATTR:
						// Arg #1: Attr name.
						// Pop #1: Subject.
						$vStack->push(AttrAccess::fetch($vStack->pop(), $op[1]));
						unset($a);
						break;

					case Machine::OP_LOAD_ITEM:
						// Pop #1: Key.
						// Pop #2: Subject.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Dereference::fetch($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_STORE_NAME:
						// Arg #1: Variable name.
						// Pop #1: Value.
						$scope->setVariable($op[1], $vStack->pop());
						unset($a);
						break;

					case Machine::OP_STORE_ATTR:
						// Arg #1: Attr name.
						// Pop #1: Subject.
						// Pop #2: Value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						AttrAccess::store($a, $op[1], $b);
						unset($a, $b);
						break;

					case Machine::OP_STORE_ITEM:
						// Arg #1: 1 if key is present on the stack, 0 if not.
						//         I.e. difference between 'a[key] = 1' vs 'a[] = 1'
						// Pop #1: Key.
						// Pop #2: Subject.
						// Pop #3: Value.
						if (empty($op[1])) {
							$a = $vStack->pop();
						}
						$b = $vStack->pop();
						$c = $vStack->pop();
						Dereference::store($b, $a ?? \null, $c);
						unset($a, $b, $c);
						break;

					case Machine::OP_POP:
						// Pop #1: Whatever is on the stack top.
						$vStack->pop();
						break;

					case Machine::OP_DUP_TOP:
						// Top: Value to be duplicated on top of stack.
						$vStack->push($vStack->top());
						break;

					case Machine::OP_SWAP:
						// Swaps top of the stack with the n-th item in the stack.
						// Arg #1: N-th.
						$vStack->swap($op[1]);
						break;

					case Machine::OP_COPY:
						// Copies n-th item from the stack to the top.
						// Arg #1: N-th.
						$vStack->copy($op[1]);
						break;

					case Machine::OP_JUMP:
						// Arg #1: Jump destination.
						$opIndex = $op[1];
						goto vm_do_index;
						break;

					case Machine::OP_JUMP_IF_F:
						// Arg #1: Jump destination.
						// Pop #1: Value to test.
						if (!$vStack->pop()->isTruthy()) {
							$opIndex = $op[1];
							goto vm_do_index;
						}
						break;

					case Machine::OP_JUMP_IF_T_OR_POP:
						// Arg #1: Jump destination.
						// Pop #1: Value to test.
						if ($vStack->top()->isTruthy()) {
							$opIndex = $op[1];
							goto vm_do_index;
						}
						$vStack->pop();
						break;

					case Machine::OP_JUMP_IF_F_OR_POP:
						// Arg #1: Jump destination.
						// Pop #1: Value to test.
						if (!$vStack->top()->isTruthy()) {
							$opIndex = $op[1];
							goto vm_do_index;
						}
						$vStack->pop();
						break;

					case Machine::OP_ADD:
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Arithmetics::add($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_SUB:
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Arithmetics::sub($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_MULTI:
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Arithmetics::multi($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_DIV:
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Arithmetics::div($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_EXP:
						// Pop #1: Right side value (factor).
						// Pop #2: Left side value (operand).
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Arithmetics::exp($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_CALL_FUNCTION:
						// Arg #1: Number N of args to pop off stack.
						// Pop N+1: Callable object.
						$a = $vStack->popToListRev($op[1]);
						$frame->storeOpIndex($opIndex);
						$b = $vStack->pop()->invoke($ctx, new CallArgs($a));
						if ($ctx->getPendingException()) {
							unset($a, $b);
							goto vm_check_exc;
						}
						$vStack->push($b);
						unset($a, $b);
						break;

					case Machine::OP_CALL_FUNCTION_EX:
						// Pop 1: Kwargs dict.
						// Pop 2: Args list.
						// Pop 3: Callable object.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$frame->storeOpIndex($opIndex);
						$c = $vStack->pop()->invoke(
							$ctx,
							new CallArgs(
								$b->getCoreValue(),
								Iteration::fromMapToVariables($a->getCoreValue()),
							),
						);
						if ($ctx->getPendingException()) {
							unset($a, $b, $c);
							goto vm_check_exc;
						}
						$vStack->push($c);
						unset($a, $b, $c);
						break;

					case Machine::OP_CALL_FUNCTION_N:
						$frame->storeOpIndex($opIndex);
						$a = $vStack->pop()->invoke($ctx);
						if ($ctx->getPendingException()) {
							unset($a);
							goto vm_check_exc;
						}
						$vStack->push($a);
						unset($a);
						break;

					case Machine::OP_BUILD_CONST_LIST:
						// Arg #1: A list array of values.
						$a = $op[1];
						$vStack->push(new ListValue($a));
						unset($a);
						break;

					case Machine::OP_BUILD_LIST:
						// Arg #1: Number of items to pop off stack.
						$a = $op[1];
						$vStack->push(new ListValue($vStack->popToListRev($op[1])));
						unset($a);
						break;

					case Machine::OP_BUILD_TUPLE:
						// Arg #1: Number of items to pop off stack.
						$vStack->push(new TupleValue($vStack->popToListRev($op[1])));
						break;

					case Machine::OP_BUILD_CONST_DICT:
						// Arg #1: A list array of key-value pairs, for example:
						//         [['key', 'value'], ...]
						$vStack->push(new DictValue($op[1]));
						unset($a, $b, $i, $pairs);
						break;

					case Machine::OP_BUILD_DICT:
						// Arg #1: Number of items to pop off stack.
						$a = $op[1] * 2;
						$b = $vStack->popToListRev($a);
						$pairs = [];
						for ($i = 0; $i < $a; $i += 2) {
							$pairs[] = [$b[0 + $i], $b[1 + $i]];
						}
						$vStack->push(new DictValue($pairs));
						unset($a, $b, $i, $pairs);
						break;

					case Machine::OP_LIST_EXTEND:
						// Pop #1: Iterable with items to extend list at TOP-1.
						$a = $vStack->pop();
						$b = $vStack->top();
						foreach (Iteration::getIteratorOfObject($a) as $c) {
							$b->itemSet(null, $c);
						}
						unset($a, $b, $c);
						break;

					case Machine::OP_LIST_APPEND:
						// Pop #1: Item to add to list at TOP-1.
						$vStack->top()->itemSet(null, $vStack->pop());
						break;

					case Machine::OP_DICT_MERGE:
						// Pop #1: Dict to merge to dict at TOP-1.
						$a = $vStack->pop();
						$b = $vStack->top();
						foreach (Iteration::fromMappingToCouples($a) as [$c, $d]) {
							$b->itemSet($c, $d);
						}
						unset($a, $b, $c, $d);
						break;

					case Machine::OP_DICT_SET_ITEM:
						// Pop #1: Item to set to dict located at TOP-1.
						// Arg #1: Key.
						$a = $vStack->pop();
						$vStack->top()->itemSet(Interned::string($op[1]), $a);
						unset($a);
						break;

					case Machine::OP_BUILD_STRING:
						// Arg #1: Number of items to pop off stack.
						$a = $vStack->popToListRev($op[1]);
						$vStack->push(
							Interned::string(Func::joinObjectsAsString($a)),
						);
						unset($a);
						break;

					case Machine::OP_COMPARE_EQ:
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Comparison::handleEqual($b, $a));
						unset($a, $b);
						break;

					case Machine::OP_COMPARE_NEQ:
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(
							Interned::bool(!Comparison::handleEqual($b, $a)->isTruthy()),
						);
						unset($a, $b);
						break;

					case Machine::OP_COMPARE_IN:
						// Arg #1: Operator text (for error message).
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Comparison::handleIn($b, $a, $op[1]));
						unset($a, $b);
						break;

					case Machine::OP_COMPARE_RELATION:
						// Arg #1: Operator text (for error message).
						// Pop #1: Right side value.
						// Pop #2: Left side value.
						$a = $vStack->pop();
						$b = $vStack->pop();
						$vStack->push(Comparison::handleRelation($b, $a, $op[1]));
						unset($a, $b);
						break;

					case Machine::OP_NEGATE:
						// Pop #1: Value.
						$vStack->push(Interned::bool(!$vStack->pop()->isTruthy()));
						break;

					case Machine::OP_CAST_BOOL:
						// Pop #1: Value.
						$vStack->push(Interned::bool($vStack->pop()->isTruthy()));
						break;

					case Machine::OP_ITER_GET:
						// Pop #1: Value to get PHP generator from.
						$vStack->push(Iteration::getIteratorOfObject($vStack->pop()));
						break;

					case Machine::OP_ITER_NEXT:
						// Top: PHP iterator object.
						$a = $vStack->top();
						$b = Iteration::getNextItem($a);
						if ($b !== Iteration::FLAG_ITERATOR_END) {
							$vStack->push($b);
						} else {
							// Iterator has finished normally - push the
							// iterator off the value stack.
							$vStack->pop();
							$opIndex = $op[1];
							goto vm_do_index;
						}
						unset($a, $b);
						break;

					case Machine::OP_UNPACK_SEQUENCE:
						// Arg #1: Expected number of items to unpack.
						// Pop #1: PHP iterator to unpack values from.
						$a = $vStack->pop();
						$b = Iteration::getIteratorOfObject($a);
						foreach (Iteration::unpack($b, $op[1]) as $item) {
							$vStack->push($item);
						}
						unset($a);
						break;

					case Machine::OP_CREATE_FUNCTION:
						// Arg #1: Function name.
						// Arg #2: BytecodeDDL object.
						// Arg #2: ParamSpec.
						$vStack->push(
							FunctionDefinition::handleCreateFunction($ctx, $op[1], $op[2], $op[3]),
						);
						break;

					case Machine::OP_TRYBLK_PUSH:
						// Arg #1: Catch label.
						$frame->pushTry(TryBlock::fromPairs(
							$op[1],
							\count($vStack),
							$scopeComp,
						));
						break;

					case Machine::OP_TRYBLK_POP:
						$frame->popTry();
						break;

					case Machine::OP_CREATE_CLASS:
						// Arg #1: Class name.
						// Arg #2: Parent type name (or null).
						// Arg #3: Class body bytecode.
						$a = ClassDefinition::handleCreateClass(
							$op[1],
							$op[2],
							$op[3],
							$ctx,
							$scope,
							$builtins,
						);
						$vStack->push($a);
						unset($a);
						break;

					case Machine::OP_EXC_THROW:
						// Pop #1: ExceptionValue.
						$a = $vStack->pop();
						$frame->storeOpIndex($opIndex);
						if (Types::isSubtypeOf($a->getType(), StaticExceptionTypes::getBaseExceptionType())) {
							$ctx->setException($a);
						} else {
							Exceptions::set(
								$ctx,
								StaticExceptionTypes::getTypeErrorType(),
								"Only exceptions can be thrown",
							);
						}
						goto vm_check_exc;
						unset($a);
						break;

					case Machine::OP_IMPORT:
						// Arg #1: Dotpath of the module to be imported.
						// Arg #2: List of names to import.
						ImportStatement::handleImport($ctx, $op[1], $op[2]);
						goto vm_check_exc;
						break;

					case Machine::OP_RETURN:
						goto vm_loop_exit;

					default:
						throw new EngineInternalError("VM unhandled op '{$op[0]}'");

				}

				goto vm_do_inc_index;

			}

		} catch (PiggybackException $pex) {

			// Receive piggybacking exception, set it into context and go handle
			// it.
			$frame->storeOpIndex($opIndex);
			Exceptions::set($ctx, $pex->excType, ...$pex->args);
			unset($pex);
			goto vm_check_exc;

		}

		vm_loop_exit:

		$ctx->setCurrentFrame($frame->getParent());
		$retval = $vStack->isEmpty()
			? Interned::null()
			: $vStack->pop();

		return $retval;

	}

}
