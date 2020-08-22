<?php

namespace Smuuf\Primi;

use \hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

	// Add these properties so PHPStan doesn't complain about undefined properties.

	/** @var int */
	public $pos;

	/** @var string */
	public $string;

	private const RESERVED_WORDS = [
		'false', 'true', 'null', 'if', 'else', 'return', 'for', 'and', 'or',
		'function', 'break', 'continue', 'while', 'try', 'catch'
	];

	/**
	 * Prevent parsing variable name which has the same name as some reserved
	 * word.
	 * Setting result to false will tell the parser that is should try other
	 * parser rules.
	 */
	protected function Mutable__finalise(&$result) {
		if (\in_array($result['text'], self::RESERVED_WORDS, \true)) {
			$result = \false;
		}
	}

/* StringLiteral: / ("[^"\\]*(\\.[^"\\]*)*")|('[^'\\]*(\\.[^'\\]*)*') /s */
protected $match_StringLiteral_typestack = ['StringLiteral'];
function match_StringLiteral () {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ ("[^"\\\\]*(\\\\.[^"\\\\]*)*")|(\'[^\'\\\\]*(\\\\.[^\'\\\\]*)*\') /s')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* NumberLiteral: / -?\d[\d_]*(\.[\d_]+)? / */
protected $match_NumberLiteral_typestack = ['NumberLiteral'];
function match_NumberLiteral () {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ -?\d[\d_]*(\.[\d_]+)? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* BoolLiteral: ( "true" | "false" ) !VariableName */
protected $match_BoolLiteral_typestack = ['BoolLiteral'];
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_10 = \null;
	do {
		$_7 = \null;
		do {
			$_5 = \null;
			do {
				$res_2 = $result;
				$pos_2 = $this->pos;
				if (($subres = $this->literal('true')) !== \false) {
					$result["text"] .= $subres;
					$_5 = \true; break;
				}
				$result = $res_2;
				$this->pos = $pos_2;
				if (($subres = $this->literal('false')) !== \false) {
					$result["text"] .= $subres;
					$_5 = \true; break;
				}
				$result = $res_2;
				$this->pos = $pos_2;
				$_5 = \false; break;
			}
			while(\false);
			if( $_5 === \false) { $_7 = \false; break; }
			$_7 = \true; break;
		}
		while(\false);
		if( $_7 === \false) { $_10 = \false; break; }
		$res_9 = $result;
		$pos_9 = $this->pos;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$result = $res_9;
			$this->pos = $pos_9;
			$_10 = \false; break;
		}
		else {
			$result = $res_9;
			$this->pos = $pos_9;
		}
		$_10 = \true; break;
	}
	while(\false);
	if( $_10 === \true ) { return $this->finalise($result); }
	if( $_10 === \false) { return \false; }
}

/* NullLiteral: "null" !VariableName */
protected $match_NullLiteral_typestack = ['NullLiteral'];
function match_NullLiteral ($stack = []) {
	$matchrule = "NullLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_14 = \null;
	do {
		if (($subres = $this->literal('null')) !== \false) { $result["text"] .= $subres; }
		else { $_14 = \false; break; }
		$res_13 = $result;
		$pos_13 = $this->pos;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$result = $res_13;
			$this->pos = $pos_13;
			$_14 = \false; break;
		}
		else {
			$result = $res_13;
			$this->pos = $pos_13;
		}
		$_14 = \true; break;
	}
	while(\false);
	if( $_14 === \true ) { return $this->finalise($result); }
	if( $_14 === \false) { return \false; }
}

/* RegexLiteral: "r" core:StringLiteral */
protected $match_RegexLiteral_typestack = ['RegexLiteral'];
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_18 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === 'r') {
			$this->pos += 1;
			$result["text"] .= 'r';
		}
		else { $_18 = \false; break; }
		$key = 'match_StringLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_StringLiteral($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_18 = \false; break; }
		$_18 = \true; break;
	}
	while(\false);
	if( $_18 === \true ) { return $this->finalise($result); }
	if( $_18 === \false) { return \false; }
}

/* Nothing: "" */
protected $match_Nothing_typestack = ['Nothing'];
function match_Nothing () {
	$matchrule = "Nothing"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:NullLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = ['Literal'];
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_36 = \null;
	do {
		$res_21 = $result;
		$pos_21 = $this->pos;
		$key = 'match_NumberLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NumberLiteral($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_36 = \true; break;
		}
		$result = $res_21;
		$this->pos = $pos_21;
		$_34 = \null;
		do {
			$res_23 = $result;
			$pos_23 = $this->pos;
			$key = 'match_StringLiteral'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_StringLiteral($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_34 = \true; break;
			}
			$result = $res_23;
			$this->pos = $pos_23;
			$_32 = \null;
			do {
				$res_25 = $result;
				$pos_25 = $this->pos;
				$key = 'match_BoolLiteral'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BoolLiteral($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_32 = \true; break;
				}
				$result = $res_25;
				$this->pos = $pos_25;
				$_30 = \null;
				do {
					$res_27 = $result;
					$pos_27 = $this->pos;
					$key = 'match_NullLiteral'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_NullLiteral($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_30 = \true; break;
					}
					$result = $res_27;
					$this->pos = $pos_27;
					$key = 'match_RegexLiteral'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_RegexLiteral($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_30 = \true; break;
					}
					$result = $res_27;
					$this->pos = $pos_27;
					$_30 = \false; break;
				}
				while(\false);
				if( $_30 === \true ) { $_32 = \true; break; }
				$result = $res_25;
				$this->pos = $pos_25;
				$_32 = \false; break;
			}
			while(\false);
			if( $_32 === \true ) { $_34 = \true; break; }
			$result = $res_23;
			$this->pos = $pos_23;
			$_34 = \false; break;
		}
		while(\false);
		if( $_34 === \true ) { $_36 = \true; break; }
		$result = $res_21;
		$this->pos = $pos_21;
		$_36 = \false; break;
	}
	while(\false);
	if( $_36 === \true ) { return $this->finalise($result); }
	if( $_36 === \false) { return \false; }
}

/* VariableName: / (?:[a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = ['VariableName'];
function match_VariableName () {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ (?:[a-zA-Z_][a-zA-Z0-9_]*) /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* Variable: core:VariableName */
protected $match_Variable_typestack = ['Variable'];
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$key = 'match_VariableName'; $pos = $this->pos;
	$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
	if ($subres !== \false) {
		$this->store($result, $subres, "core");
		return $this->finalise($result);
	}
	else { return \false; }
}

/* AnonymousFunction: "function" __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ params:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = ['AnonymousFunction'];
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_63 = \null;
	do {
		$res_40 = $result;
		$pos_40 = $this->pos;
		$_50 = \null;
		do {
			if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
			else { $_50 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_50 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_50 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_50 = \false; break; }
			$res_45 = $result;
			$pos_45 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_45;
				$this->pos = $pos_45;
				unset($res_45, $pos_45);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_50 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_50 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_50 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_50 = \false; break; }
			$_50 = \true; break;
		}
		while(\false);
		if( $_50 === \true ) { $_63 = \true; break; }
		$result = $res_40;
		$this->pos = $pos_40;
		$_61 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_61 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_61 = \false; break; }
			$res_54 = $result;
			$pos_54 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_54;
				$this->pos = $pos_54;
				unset($res_54, $pos_54);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_61 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_61 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_61 = \false; break; }
			if (($subres = $this->literal('=>')) !== \false) { $result["text"] .= $subres; }
			else { $_61 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_61 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_61 = \false; break; }
			$_61 = \true; break;
		}
		while(\false);
		if( $_61 === \true ) { $_63 = \true; break; }
		$result = $res_40;
		$this->pos = $pos_40;
		$_63 = \false; break;
	}
	while(\false);
	if( $_63 === \true ) { return $this->finalise($result); }
	if( $_63 === \false) { return \false; }
}

/* DictItem: __ key:Expression __ ":" __ value:Expression __ */
protected $match_DictItem_typestack = ['DictItem'];
function match_DictItem ($stack = []) {
	$matchrule = "DictItem"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_72 = \null;
	do {
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_72 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_72 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_72 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_72 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_72 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "value");
		}
		else { $_72 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_72 = \false; break; }
		$_72 = \true; break;
	}
	while(\false);
	if( $_72 === \true ) { return $this->finalise($result); }
	if( $_72 === \false) { return \false; }
}

/* DictDefinition: "{" __ ( items:DictItem ( __ "," __ items:DictItem )* )? __ ( "," __ )? "}" */
protected $match_DictDefinition_typestack = ['DictDefinition'];
function match_DictDefinition ($stack = []) {
	$matchrule = "DictDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_91 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_91 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_91 = \false; break; }
		$res_84 = $result;
		$pos_84 = $this->pos;
		$_83 = \null;
		do {
			$key = 'match_DictItem'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_DictItem($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_83 = \false; break; }
			while (\true) {
				$res_82 = $result;
				$pos_82 = $this->pos;
				$_81 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_81 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_81 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_81 = \false; break; }
					$key = 'match_DictItem'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_DictItem($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_81 = \false; break; }
					$_81 = \true; break;
				}
				while(\false);
				if( $_81 === \false) {
					$result = $res_82;
					$this->pos = $pos_82;
					unset($res_82, $pos_82);
					break;
				}
			}
			$_83 = \true; break;
		}
		while(\false);
		if( $_83 === \false) {
			$result = $res_84;
			$this->pos = $pos_84;
			unset($res_84, $pos_84);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_91 = \false; break; }
		$res_89 = $result;
		$pos_89 = $this->pos;
		$_88 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_88 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_88 = \false; break; }
			$_88 = \true; break;
		}
		while(\false);
		if( $_88 === \false) {
			$result = $res_89;
			$this->pos = $pos_89;
			unset($res_89, $pos_89);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_91 = \false; break; }
		$_91 = \true; break;
	}
	while(\false);
	if( $_91 === \true ) { return $this->finalise($result); }
	if( $_91 === \false) { return \false; }
}

/* ListDefinition: "[" __ ( items:Expression ( __ "," __ items:Expression )* )? __ ( "," __ )? "]" */
protected $match_ListDefinition_typestack = ['ListDefinition'];
function match_ListDefinition ($stack = []) {
	$matchrule = "ListDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_110 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_110 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_110 = \false; break; }
		$res_103 = $result;
		$pos_103 = $this->pos;
		$_102 = \null;
		do {
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_102 = \false; break; }
			while (\true) {
				$res_101 = $result;
				$pos_101 = $this->pos;
				$_100 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_100 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_100 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_100 = \false; break; }
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_100 = \false; break; }
					$_100 = \true; break;
				}
				while(\false);
				if( $_100 === \false) {
					$result = $res_101;
					$this->pos = $pos_101;
					unset($res_101, $pos_101);
					break;
				}
			}
			$_102 = \true; break;
		}
		while(\false);
		if( $_102 === \false) {
			$result = $res_103;
			$this->pos = $pos_103;
			unset($res_103, $pos_103);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_110 = \false; break; }
		$res_108 = $result;
		$pos_108 = $this->pos;
		$_107 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_107 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_107 = \false; break; }
			$_107 = \true; break;
		}
		while(\false);
		if( $_107 === \false) {
			$result = $res_108;
			$this->pos = $pos_108;
			unset($res_108, $pos_108);
		}
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_110 = \false; break; }
		$_110 = \true; break;
	}
	while(\false);
	if( $_110 === \true ) { return $this->finalise($result); }
	if( $_110 === \false) { return \false; }
}

/* Value: skip:Literal | skip:Variable | skip:ListDefinition | skip:DictDefinition */
protected $match_Value_typestack = ['Value'];
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_123 = \null;
	do {
		$res_112 = $result;
		$pos_112 = $this->pos;
		$key = 'match_Literal'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Literal($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_123 = \true; break;
		}
		$result = $res_112;
		$this->pos = $pos_112;
		$_121 = \null;
		do {
			$res_114 = $result;
			$pos_114 = $this->pos;
			$key = 'match_Variable'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Variable($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_121 = \true; break;
			}
			$result = $res_114;
			$this->pos = $pos_114;
			$_119 = \null;
			do {
				$res_116 = $result;
				$pos_116 = $this->pos;
				$key = 'match_ListDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ListDefinition($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_119 = \true; break;
				}
				$result = $res_116;
				$this->pos = $pos_116;
				$key = 'match_DictDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_DictDefinition($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_119 = \true; break;
				}
				$result = $res_116;
				$this->pos = $pos_116;
				$_119 = \false; break;
			}
			while(\false);
			if( $_119 === \true ) { $_121 = \true; break; }
			$result = $res_114;
			$this->pos = $pos_114;
			$_121 = \false; break;
		}
		while(\false);
		if( $_121 === \true ) { $_123 = \true; break; }
		$result = $res_112;
		$this->pos = $pos_112;
		$_123 = \false; break;
	}
	while(\false);
	if( $_123 === \true ) { return $this->finalise($result); }
	if( $_123 === \false) { return \false; }
}

/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = ['VariableVector'];
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_127 = \null;
	do {
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_127 = \false; break; }
		$key = 'match_Vector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Vector($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else { $_127 = \false; break; }
		$_127 = \true; break;
	}
	while(\false);
	if( $_127 === \true ) { return $this->finalise($result); }
	if( $_127 === \false) { return \false; }
}

/* Vector: ( "[" __ ( index:Expression | index:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = ['Vector'];
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_143 = \null;
	do {
		$_140 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_140 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_140 = \false; break; }
			$_136 = \null;
			do {
				$_134 = \null;
				do {
					$res_131 = $result;
					$pos_131 = $this->pos;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "index");
						$_134 = \true; break;
					}
					$result = $res_131;
					$this->pos = $pos_131;
					$key = 'match_Nothing'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Nothing($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "index");
						$_134 = \true; break;
					}
					$result = $res_131;
					$this->pos = $pos_131;
					$_134 = \false; break;
				}
				while(\false);
				if( $_134 === \false) { $_136 = \false; break; }
				$_136 = \true; break;
			}
			while(\false);
			if( $_136 === \false) { $_140 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_140 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_140 = \false; break; }
			$_140 = \true; break;
		}
		while(\false);
		if( $_140 === \false) { $_143 = \false; break; }
		$res_142 = $result;
		$pos_142 = $this->pos;
		$key = 'match_Vector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Vector($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else {
			$result = $res_142;
			$this->pos = $pos_142;
			unset($res_142, $pos_142);
		}
		$_143 = \true; break;
	}
	while(\false);
	if( $_143 === \true ) { return $this->finalise($result); }
	if( $_143 === \false) { return \false; }
}

/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = ['Mutable'];
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_148 = \null;
	do {
		$res_145 = $result;
		$pos_145 = $this->pos;
		$key = 'match_VariableVector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableVector($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_148 = \true; break;
		}
		$result = $res_145;
		$this->pos = $pos_145;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_148 = \true; break;
		}
		$result = $res_145;
		$this->pos = $pos_145;
		$_148 = \false; break;
	}
	while(\false);
	if( $_148 === \true ) { return $this->finalise($result); }
	if( $_148 === \false) { return \false; }
}

/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = ['AddOperator'];
function match_AddOperator () {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule);
	$_153 = \null;
	do {
		$res_150 = $result;
		$pos_150 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_153 = \true; break;
		}
		$result = $res_150;
		$this->pos = $pos_150;
		if (\substr($this->string, $this->pos, 1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_153 = \true; break;
		}
		$result = $res_150;
		$this->pos = $pos_150;
		$_153 = \false; break;
	}
	while(\false);
	if( $_153 === \true ) { return $this->finalise($result); }
	if( $_153 === \false) { return \false; }
}

/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = ['MultiplyOperator'];
function match_MultiplyOperator () {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule);
	$_158 = \null;
	do {
		$res_155 = $result;
		$pos_155 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_158 = \true; break;
		}
		$result = $res_155;
		$this->pos = $pos_155;
		if (\substr($this->string, $this->pos, 1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_158 = \true; break;
		}
		$result = $res_155;
		$this->pos = $pos_155;
		$_158 = \false; break;
	}
	while(\false);
	if( $_158 === \true ) { return $this->finalise($result); }
	if( $_158 === \false) { return \false; }
}

/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = ['AssignmentOperator'];
function match_AssignmentOperator () {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}

/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = ['ComparisonOperator'];
function match_ComparisonOperator () {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule);
	$_180 = \null;
	do {
		$res_161 = $result;
		$pos_161 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_180 = \true; break;
		}
		$result = $res_161;
		$this->pos = $pos_161;
		$_178 = \null;
		do {
			$res_163 = $result;
			$pos_163 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_178 = \true; break;
			}
			$result = $res_163;
			$this->pos = $pos_163;
			$_176 = \null;
			do {
				$res_165 = $result;
				$pos_165 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_176 = \true; break;
				}
				$result = $res_165;
				$this->pos = $pos_165;
				$_174 = \null;
				do {
					$res_167 = $result;
					$pos_167 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_174 = \true; break;
					}
					$result = $res_167;
					$this->pos = $pos_167;
					$_172 = \null;
					do {
						$res_169 = $result;
						$pos_169 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_172 = \true; break;
						}
						$result = $res_169;
						$this->pos = $pos_169;
						if (\substr($this->string, $this->pos, 1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_172 = \true; break;
						}
						$result = $res_169;
						$this->pos = $pos_169;
						$_172 = \false; break;
					}
					while(\false);
					if( $_172 === \true ) { $_174 = \true; break; }
					$result = $res_167;
					$this->pos = $pos_167;
					$_174 = \false; break;
				}
				while(\false);
				if( $_174 === \true ) { $_176 = \true; break; }
				$result = $res_165;
				$this->pos = $pos_165;
				$_176 = \false; break;
			}
			while(\false);
			if( $_176 === \true ) { $_178 = \true; break; }
			$result = $res_163;
			$this->pos = $pos_163;
			$_178 = \false; break;
		}
		while(\false);
		if( $_178 === \true ) { $_180 = \true; break; }
		$result = $res_161;
		$this->pos = $pos_161;
		$_180 = \false; break;
	}
	while(\false);
	if( $_180 === \true ) { return $this->finalise($result); }
	if( $_180 === \false) { return \false; }
}

/* AndOperator: "and" */
protected $match_AndOperator_typestack = ['AndOperator'];
function match_AndOperator () {
	$matchrule = "AndOperator"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('and')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* OrOperator: "or" */
protected $match_OrOperator_typestack = ['OrOperator'];
function match_OrOperator () {
	$matchrule = "OrOperator"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('or')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* NegationOperator: "!" */
protected $match_NegationOperator_typestack = ['NegationOperator'];
function match_NegationOperator () {
	$matchrule = "NegationOperator"; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '!') {
		$this->pos += 1;
		$result["text"] .= '!';
		return $this->finalise($result);
	}
	else { return \false; }
}

/* Expression: skip:AnonymousFunction | skip:Assignment | skip:CondExpr */
protected $match_Expression_typestack = ['Expression'];
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_192 = \null;
	do {
		$res_185 = $result;
		$pos_185 = $this->pos;
		$key = 'match_AnonymousFunction'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AnonymousFunction($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_192 = \true; break;
		}
		$result = $res_185;
		$this->pos = $pos_185;
		$_190 = \null;
		do {
			$res_187 = $result;
			$pos_187 = $this->pos;
			$key = 'match_Assignment'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Assignment($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_190 = \true; break;
			}
			$result = $res_187;
			$this->pos = $pos_187;
			$key = 'match_CondExpr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_CondExpr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_190 = \true; break;
			}
			$result = $res_187;
			$this->pos = $pos_187;
			$_190 = \false; break;
		}
		while(\false);
		if( $_190 === \true ) { $_192 = \true; break; }
		$result = $res_185;
		$this->pos = $pos_185;
		$_192 = \false; break;
	}
	while(\false);
	if( $_192 === \true ) { return $this->finalise($result); }
	if( $_192 === \false) { return \false; }
}

/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_199 = \null;
	do {
		$key = 'match_Mutable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Mutable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_199 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_199 = \false; break; }
		$key = 'match_AssignmentOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AssignmentOperator($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_199 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_199 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_199 = \false; break; }
		$_199 = \true; break;
	}
	while(\false);
	if( $_199 === \true ) { return $this->finalise($result); }
	if( $_199 === \false) { return \false; }
}

/* CondExpr: true:LogicalOr ( ] "if" __ "(" __ cond:Expression __ ")" __ "else" ] false:LogicalOr )? */
protected $match_CondExpr_typestack = ['CondExpr'];
function match_CondExpr ($stack = []) {
	$matchrule = "CondExpr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_216 = \null;
	do {
		$key = 'match_LogicalOr'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "true");
		}
		else { $_216 = \false; break; }
		$res_215 = $result;
		$pos_215 = $this->pos;
		$_214 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_214 = \false; break; }
			if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
			else { $_214 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_214 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_214 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_214 = \false; break; }
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "cond");
			}
			else { $_214 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_214 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_214 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_214 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_214 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_214 = \false; break; }
			$key = 'match_LogicalOr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "false");
			}
			else { $_214 = \false; break; }
			$_214 = \true; break;
		}
		while(\false);
		if( $_214 === \false) {
			$result = $res_215;
			$this->pos = $pos_215;
			unset($res_215, $pos_215);
		}
		$_216 = \true; break;
	}
	while(\false);
	if( $_216 === \true ) { return $this->finalise($result); }
	if( $_216 === \false) { return \false; }
}

/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_225 = \null;
	do {
		$key = 'match_LogicalAnd'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_225 = \false; break; }
		while (\true) {
			$res_224 = $result;
			$pos_224 = $this->pos;
			$_223 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_223 = \false; break; }
				$key = 'match_OrOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_OrOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_223 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_223 = \false; break; }
				$key = 'match_LogicalAnd'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_223 = \false; break; }
				$_223 = \true; break;
			}
			while(\false);
			if( $_223 === \false) {
				$result = $res_224;
				$this->pos = $pos_224;
				unset($res_224, $pos_224);
				break;
			}
		}
		$_225 = \true; break;
	}
	while(\false);
	if( $_225 === \true ) { return $this->finalise($result); }
	if( $_225 === \false) { return \false; }
}

/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_234 = \null;
	do {
		$key = 'match_Comparison'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_234 = \false; break; }
		while (\true) {
			$res_233 = $result;
			$pos_233 = $this->pos;
			$_232 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_232 = \false; break; }
				$key = 'match_AndOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AndOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_232 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_232 = \false; break; }
				$key = 'match_Comparison'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_232 = \false; break; }
				$_232 = \true; break;
			}
			while(\false);
			if( $_232 === \false) {
				$result = $res_233;
				$this->pos = $pos_233;
				unset($res_233, $pos_233);
				break;
			}
		}
		$_234 = \true; break;
	}
	while(\false);
	if( $_234 === \true ) { return $this->finalise($result); }
	if( $_234 === \false) { return \false; }
}

/* Comparison: operands:Addition ( __ ops:ComparisonOperator __ operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_243 = \null;
	do {
		$key = 'match_Addition'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Addition($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_243 = \false; break; }
		while (\true) {
			$res_242 = $result;
			$pos_242 = $this->pos;
			$_241 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_241 = \false; break; }
				$key = 'match_ComparisonOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ComparisonOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_241 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_241 = \false; break; }
				$key = 'match_Addition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Addition($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_241 = \false; break; }
				$_241 = \true; break;
			}
			while(\false);
			if( $_241 === \false) {
				$result = $res_242;
				$this->pos = $pos_242;
				unset($res_242, $pos_242);
				break;
			}
		}
		$_243 = \true; break;
	}
	while(\false);
	if( $_243 === \true ) { return $this->finalise($result); }
	if( $_243 === \false) { return \false; }
}

/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_252 = \null;
	do {
		$key = 'match_Multiplication'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_252 = \false; break; }
		while (\true) {
			$res_251 = $result;
			$pos_251 = $this->pos;
			$_250 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_250 = \false; break; }
				$key = 'match_AddOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AddOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_250 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_250 = \false; break; }
				$key = 'match_Multiplication'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_250 = \false; break; }
				$_250 = \true; break;
			}
			while(\false);
			if( $_250 === \false) {
				$result = $res_251;
				$this->pos = $pos_251;
				unset($res_251, $pos_251);
				break;
			}
		}
		$_252 = \true; break;
	}
	while(\false);
	if( $_252 === \true ) { return $this->finalise($result); }
	if( $_252 === \false) { return \false; }
}

/* Multiplication: operands:Negation ( __ ops:MultiplyOperator __ operands:Negation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_261 = \null;
	do {
		$key = 'match_Negation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Negation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_261 = \false; break; }
		while (\true) {
			$res_260 = $result;
			$pos_260 = $this->pos;
			$_259 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_259 = \false; break; }
				$key = 'match_MultiplyOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_MultiplyOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_259 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_259 = \false; break; }
				$key = 'match_Negation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Negation($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_259 = \false; break; }
				$_259 = \true; break;
			}
			while(\false);
			if( $_259 === \false) {
				$result = $res_260;
				$this->pos = $pos_260;
				unset($res_260, $pos_260);
				break;
			}
		}
		$_261 = \true; break;
	}
	while(\false);
	if( $_261 === \true ) { return $this->finalise($result); }
	if( $_261 === \false) { return \false; }
}

/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_267 = \null;
	do {
		while (\true) {
			$res_265 = $result;
			$pos_265 = $this->pos;
			$_264 = \null;
			do {
				$key = 'match_NegationOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_NegationOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_264 = \false; break; }
				$_264 = \true; break;
			}
			while(\false);
			if( $_264 === \false) {
				$result = $res_265;
				$this->pos = $pos_265;
				unset($res_265, $pos_265);
				break;
			}
		}
		$key = 'match_Operand'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Operand($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_267 = \false; break; }
		$_267 = \true; break;
	}
	while(\false);
	if( $_267 === \true ) { return $this->finalise($result); }
	if( $_267 === \false) { return \false; }
}

/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_287 = \null;
	do {
		$res_269 = $result;
		$pos_269 = $this->pos;
		$_284 = \null;
		do {
			$_281 = \null;
			do {
				$_279 = \null;
				do {
					$res_270 = $result;
					$pos_270 = $this->pos;
					$_276 = \null;
					do {
						if (\substr($this->string, $this->pos, 1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_276 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_276 = \false; break; }
						$key = 'match_Expression'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_Expression($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_276 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_276 = \false; break; }
						if (\substr($this->string, $this->pos, 1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_276 = \false; break; }
						$_276 = \true; break;
					}
					while(\false);
					if( $_276 === \true ) { $_279 = \true; break; }
					$result = $res_270;
					$this->pos = $pos_270;
					$key = 'match_Value'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Value($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_279 = \true; break;
					}
					$result = $res_270;
					$this->pos = $pos_270;
					$_279 = \false; break;
				}
				while(\false);
				if( $_279 === \false) { $_281 = \false; break; }
				$_281 = \true; break;
			}
			while(\false);
			if( $_281 === \false) { $_284 = \false; break; }
			$res_283 = $result;
			$pos_283 = $this->pos;
			$key = 'match_Chain'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Chain($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_283;
				$this->pos = $pos_283;
				unset($res_283, $pos_283);
			}
			$_284 = \true; break;
		}
		while(\false);
		if( $_284 === \true ) { $_287 = \true; break; }
		$result = $res_269;
		$this->pos = $pos_269;
		$key = 'match_Value'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Value($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_287 = \true; break;
		}
		$result = $res_269;
		$this->pos = $pos_269;
		$_287 = \false; break;
	}
	while(\false);
	if( $_287 === \true ) { return $this->finalise($result); }
	if( $_287 === \false) { return \false; }
}

/* Chain: &/[\[\(\.]/ ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_302 = \null;
	do {
		$res_289 = $result;
		$pos_289 = $this->pos;
		if (($subres = $this->rx('/[\[\(\.]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_289;
			$this->pos = $pos_289;
		}
		else {
			$result = $res_289;
			$this->pos = $pos_289;
			$_302 = \false; break;
		}
		$_299 = \null;
		do {
			$_297 = \null;
			do {
				$res_290 = $result;
				$pos_290 = $this->pos;
				$key = 'match_Dereference'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Dereference($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_297 = \true; break;
				}
				$result = $res_290;
				$this->pos = $pos_290;
				$_295 = \null;
				do {
					$res_292 = $result;
					$pos_292 = $this->pos;
					$key = 'match_Invocation'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_295 = \true; break;
					}
					$result = $res_292;
					$this->pos = $pos_292;
					$key = 'match_ChainedFunction'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ChainedFunction($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_295 = \true; break;
					}
					$result = $res_292;
					$this->pos = $pos_292;
					$_295 = \false; break;
				}
				while(\false);
				if( $_295 === \true ) { $_297 = \true; break; }
				$result = $res_290;
				$this->pos = $pos_290;
				$_297 = \false; break;
			}
			while(\false);
			if( $_297 === \false) { $_299 = \false; break; }
			$_299 = \true; break;
		}
		while(\false);
		if( $_299 === \false) { $_302 = \false; break; }
		$res_301 = $result;
		$pos_301 = $this->pos;
		$key = 'match_Chain'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Chain($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_301;
			$this->pos = $pos_301;
			unset($res_301, $pos_301);
		}
		$_302 = \true; break;
	}
	while(\false);
	if( $_302 === \true ) { return $this->finalise($result); }
	if( $_302 === \false) { return \false; }
}

/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_309 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_309 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_309 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_309 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_309 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_309 = \false; break; }
		$_309 = \true; break;
	}
	while(\false);
	if( $_309 === \true ) { return $this->finalise($result); }
	if( $_309 === \false) { return \false; }
}

/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_316 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_316 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_316 = \false; break; }
		$res_313 = $result;
		$pos_313 = $this->pos;
		$key = 'match_ArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_313;
			$this->pos = $pos_313;
			unset($res_313, $pos_313);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_316 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_316 = \false; break; }
		$_316 = \true; break;
	}
	while(\false);
	if( $_316 === \true ) { return $this->finalise($result); }
	if( $_316 === \false) { return \false; }
}

/* ChainedFunction: "." fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_321 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_321 = \false; break; }
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_321 = \false; break; }
		$key = 'match_Invocation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_321 = \false; break; }
		$_321 = \true; break;
	}
	while(\false);
	if( $_321 === \true ) { return $this->finalise($result); }
	if( $_321 === \false) { return \false; }
}

/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_330 = \null;
	do {
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_330 = \false; break; }
		while (\true) {
			$res_329 = $result;
			$pos_329 = $this->pos;
			$_328 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_328 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_328 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_328 = \false; break; }
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_328 = \false; break; }
				$_328 = \true; break;
			}
			while(\false);
			if( $_328 === \false) {
				$result = $res_329;
				$this->pos = $pos_329;
				unset($res_329, $pos_329);
				break;
			}
		}
		$_330 = \true; break;
	}
	while(\false);
	if( $_330 === \true ) { return $this->finalise($result); }
	if( $_330 === \false) { return \false; }
}

/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_339 = \null;
	do {
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_339 = \false; break; }
		while (\true) {
			$res_338 = $result;
			$pos_338 = $this->pos;
			$_337 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_337 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_337 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_337 = \false; break; }
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_337 = \false; break; }
				$_337 = \true; break;
			}
			while(\false);
			if( $_337 === \false) {
				$result = $res_338;
				$this->pos = $pos_338;
				unset($res_338, $pos_338);
				break;
			}
		}
		$_339 = \true; break;
	}
	while(\false);
	if( $_339 === \true ) { return $this->finalise($result); }
	if( $_339 === \false) { return \false; }
}

/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_352 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_352 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_352 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_352 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_352 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_352 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_352 = \false; break; }
		$res_347 = $result;
		$pos_347 = $this->pos;
		$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_347;
			$this->pos = $pos_347;
			unset($res_347, $pos_347);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_352 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_352 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_352 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_352 = \false; break; }
		$_352 = \true; break;
	}
	while(\false);
	if( $_352 === \true ) { return $this->finalise($result); }
	if( $_352 === \false) { return \false; }
}

/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_373 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_373 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_373 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_373 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_373 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_373 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_373 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_373 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_373 = \false; break; }
		$_363 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_363 = \false; break; }
			$_363 = \true; break;
		}
		while(\false);
		if( $_363 === \false) { $_373 = \false; break; }
		$res_372 = $result;
		$pos_372 = $this->pos;
		$_371 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_371 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_371 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_371 = \false; break; }
			$_369 = \null;
			do {
				$key = 'match_Block'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Block($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_369 = \false; break; }
				$_369 = \true; break;
			}
			while(\false);
			if( $_369 === \false) { $_371 = \false; break; }
			$_371 = \true; break;
		}
		while(\false);
		if( $_371 === \false) {
			$result = $res_372;
			$this->pos = $pos_372;
			unset($res_372, $pos_372);
		}
		$_373 = \true; break;
	}
	while(\false);
	if( $_373 === \true ) { return $this->finalise($result); }
	if( $_373 === \false) { return \false; }
}

/* ForStatement: "for" __ "(" __ item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_390 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_390 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_390 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_390 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_390 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_390 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_390 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_390 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_390 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_390 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_390 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_390 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_390 = \false; break; }
		$_388 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_388 = \false; break; }
			$_388 = \true; break;
		}
		while(\false);
		if( $_388 === \false) { $_390 = \false; break; }
		$_390 = \true; break;
	}
	while(\false);
	if( $_390 === \true ) { return $this->finalise($result); }
	if( $_390 === \false) { return \false; }
}

/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_403 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_403 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_403 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_403 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_403 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_403 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_403 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_403 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_403 = \false; break; }
		$_401 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_401 = \false; break; }
			$_401 = \true; break;
		}
		while(\false);
		if( $_401 === \false) { $_403 = \false; break; }
		$_403 = \true; break;
	}
	while(\false);
	if( $_403 === \true ) { return $this->finalise($result); }
	if( $_403 === \false) { return \false; }
}

/* TryStatement: "try" __ main:Block __ "catch" __ onerror:Block */
protected $match_TryStatement_typestack = ['TryStatement'];
function match_TryStatement ($stack = []) {
	$matchrule = "TryStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_412 = \null;
	do {
		if (($subres = $this->literal('try')) !== \false) { $result["text"] .= $subres; }
		else { $_412 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_412 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "main");
		}
		else { $_412 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_412 = \false; break; }
		if (($subres = $this->literal('catch')) !== \false) { $result["text"] .= $subres; }
		else { $_412 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_412 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "onerror");
		}
		else { $_412 = \false; break; }
		$_412 = \true; break;
	}
	while(\false);
	if( $_412 === \true ) { return $this->finalise($result); }
	if( $_412 === \false) { return \false; }
}

/* CommandStatements: &/[rbc]/ ( skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement ) */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_426 = \null;
	do {
		$res_414 = $result;
		$pos_414 = $this->pos;
		if (($subres = $this->rx('/[rbc]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_414;
			$this->pos = $pos_414;
		}
		else {
			$result = $res_414;
			$this->pos = $pos_414;
			$_426 = \false; break;
		}
		$_424 = \null;
		do {
			$_422 = \null;
			do {
				$res_415 = $result;
				$pos_415 = $this->pos;
				$key = 'match_ReturnStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ReturnStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_422 = \true; break;
				}
				$result = $res_415;
				$this->pos = $pos_415;
				$_420 = \null;
				do {
					$res_417 = $result;
					$pos_417 = $this->pos;
					$key = 'match_BreakStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_BreakStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_420 = \true; break;
					}
					$result = $res_417;
					$this->pos = $pos_417;
					$key = 'match_ContinueStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ContinueStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_420 = \true; break;
					}
					$result = $res_417;
					$this->pos = $pos_417;
					$_420 = \false; break;
				}
				while(\false);
				if( $_420 === \true ) { $_422 = \true; break; }
				$result = $res_415;
				$this->pos = $pos_415;
				$_422 = \false; break;
			}
			while(\false);
			if( $_422 === \false) { $_424 = \false; break; }
			$_424 = \true; break;
		}
		while(\false);
		if( $_424 === \false) { $_426 = \false; break; }
		$_426 = \true; break;
	}
	while(\false);
	if( $_426 === \true ) { return $this->finalise($result); }
	if( $_426 === \false) { return \false; }
}

/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_440 = \null;
	do {
		$res_428 = $result;
		$pos_428 = $this->pos;
		$_434 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_434 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_434 = \false; break; }
			$res_433 = $result;
			$pos_433 = $this->pos;
			$_432 = \null;
			do {
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_432 = \false; break; }
				$_432 = \true; break;
			}
			while(\false);
			if( $_432 === \false) {
				$result = $res_433;
				$this->pos = $pos_433;
				unset($res_433, $pos_433);
			}
			$_434 = \true; break;
		}
		while(\false);
		if( $_434 === \true ) { $_440 = \true; break; }
		$result = $res_428;
		$this->pos = $pos_428;
		$_438 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_438 = \false; break; }
			$res_437 = $result;
			$pos_437 = $this->pos;
			$key = 'match_SEP'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_SEP($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_437;
				$this->pos = $pos_437;
			}
			else {
				$result = $res_437;
				$this->pos = $pos_437;
				$_438 = \false; break;
			}
			$_438 = \true; break;
		}
		while(\false);
		if( $_438 === \true ) { $_440 = \true; break; }
		$result = $res_428;
		$this->pos = $pos_428;
		$_440 = \false; break;
	}
	while(\false);
	if( $_440 === \true ) { return $this->finalise($result); }
	if( $_440 === \false) { return \false; }
}

/* BreakStatement: "break" */
protected $match_BreakStatement_typestack = ['BreakStatement'];
function match_BreakStatement () {
	$matchrule = "BreakStatement"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('break')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* ContinueStatement: "continue" */
protected $match_ContinueStatement_typestack = ['ContinueStatement'];
function match_ContinueStatement () {
	$matchrule = "ContinueStatement"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('continue')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* BlockStatements: &/[iwft]/ ( skip:IfStatement | skip:WhileStatement | skip:FunctionDefinition | skip:ForStatement | skip:TryStatement) */
protected $match_BlockStatements_typestack = ['BlockStatements'];
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_464 = \null;
	do {
		$res_444 = $result;
		$pos_444 = $this->pos;
		if (($subres = $this->rx('/[iwft]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_444;
			$this->pos = $pos_444;
		}
		else {
			$result = $res_444;
			$this->pos = $pos_444;
			$_464 = \false; break;
		}
		$_462 = \null;
		do {
			$_460 = \null;
			do {
				$res_445 = $result;
				$pos_445 = $this->pos;
				$key = 'match_IfStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_IfStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_460 = \true; break;
				}
				$result = $res_445;
				$this->pos = $pos_445;
				$_458 = \null;
				do {
					$res_447 = $result;
					$pos_447 = $this->pos;
					$key = 'match_WhileStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_WhileStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_458 = \true; break;
					}
					$result = $res_447;
					$this->pos = $pos_447;
					$_456 = \null;
					do {
						$res_449 = $result;
						$pos_449 = $this->pos;
						$key = 'match_FunctionDefinition'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_FunctionDefinition($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_456 = \true; break;
						}
						$result = $res_449;
						$this->pos = $pos_449;
						$_454 = \null;
						do {
							$res_451 = $result;
							$pos_451 = $this->pos;
							$key = 'match_ForStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ForStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_454 = \true; break;
							}
							$result = $res_451;
							$this->pos = $pos_451;
							$key = 'match_TryStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_TryStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_454 = \true; break;
							}
							$result = $res_451;
							$this->pos = $pos_451;
							$_454 = \false; break;
						}
						while(\false);
						if( $_454 === \true ) { $_456 = \true; break; }
						$result = $res_449;
						$this->pos = $pos_449;
						$_456 = \false; break;
					}
					while(\false);
					if( $_456 === \true ) { $_458 = \true; break; }
					$result = $res_447;
					$this->pos = $pos_447;
					$_458 = \false; break;
				}
				while(\false);
				if( $_458 === \true ) { $_460 = \true; break; }
				$result = $res_445;
				$this->pos = $pos_445;
				$_460 = \false; break;
			}
			while(\false);
			if( $_460 === \false) { $_462 = \false; break; }
			$_462 = \true; break;
		}
		while(\false);
		if( $_462 === \false) { $_464 = \false; break; }
		$_464 = \true; break;
	}
	while(\false);
	if( $_464 === \true ) { return $this->finalise($result); }
	if( $_464 === \false) { return \false; }
}

/* Statement: !/[\s\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_478 = \null;
	do {
		$res_466 = $result;
		$pos_466 = $this->pos;
		if (($subres = $this->rx('/[\s\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_466;
			$this->pos = $pos_466;
			$_478 = \false; break;
		}
		else {
			$result = $res_466;
			$this->pos = $pos_466;
		}
		$_476 = \null;
		do {
			$_474 = \null;
			do {
				$res_467 = $result;
				$pos_467 = $this->pos;
				$key = 'match_BlockStatements'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BlockStatements($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_474 = \true; break;
				}
				$result = $res_467;
				$this->pos = $pos_467;
				$_472 = \null;
				do {
					$res_469 = $result;
					$pos_469 = $this->pos;
					$key = 'match_CommandStatements'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_CommandStatements($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_472 = \true; break;
					}
					$result = $res_469;
					$this->pos = $pos_469;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_472 = \true; break;
					}
					$result = $res_469;
					$this->pos = $pos_469;
					$_472 = \false; break;
				}
				while(\false);
				if( $_472 === \true ) { $_474 = \true; break; }
				$result = $res_467;
				$this->pos = $pos_467;
				$_474 = \false; break;
			}
			while(\false);
			if( $_474 === \false) { $_476 = \false; break; }
			$_476 = \true; break;
		}
		while(\false);
		if( $_476 === \false) { $_478 = \false; break; }
		$_478 = \true; break;
	}
	while(\false);
	if( $_478 === \true ) { return $this->finalise($result); }
	if( $_478 === \false) { return \false; }
}

/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_486 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_486 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_486 = \false; break; }
		$res_484 = $result;
		$pos_484 = $this->pos;
		$_483 = \null;
		do {
			$key = 'match_Program'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Program($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_483 = \false; break; }
			$_483 = \true; break;
		}
		while(\false);
		if( $_483 === \false) {
			$result = $res_484;
			$this->pos = $pos_484;
			unset($res_484, $pos_484);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_486 = \false; break; }
		$_486 = \true; break;
	}
	while(\false);
	if( $_486 === \true ) { return $this->finalise($result); }
	if( $_486 === \false) { return \false; }
}

/* __: / [\s]*+(?:\/\/[^\n]*+(?:\s*+))? / */
protected $match____typestack = ['__'];
function match___ () {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ [\s]*+(?:\/\/[^\n]*+(?:\s*+))? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = ['NL'];
function match_NL () {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ (?:\/\/[^\n]*)?\n /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}

/* SEP: ";" | NL */
protected $match_SEP_typestack = ['SEP'];
function match_SEP ($stack = []) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_493 = \null;
	do {
		$res_490 = $result;
		$pos_490 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_493 = \true; break;
		}
		$result = $res_490;
		$this->pos = $pos_490;
		$key = 'match_NL'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NL($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_493 = \true; break;
		}
		$result = $res_490;
		$this->pos = $pos_490;
		$_493 = \false; break;
	}
	while(\false);
	if( $_493 === \true ) { return $this->finalise($result); }
	if( $_493 === \false) { return \false; }
}

/* Program: ( __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_502 = \null;
	do {
		while (\true) {
			$res_500 = $result;
			$pos_500 = $this->pos;
			$_499 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_499 = \false; break; }
				$res_496 = $result;
				$pos_496 = $this->pos;
				$key = 'match_Statement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Statement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_496;
					$this->pos = $pos_496;
					unset($res_496, $pos_496);
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$key = 'match_SEP'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_SEP($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_499 = \false; break; }
				$_499 = \true; break;
			}
			while(\false);
			if( $_499 === \false) {
				$result = $res_500;
				$this->pos = $pos_500;
				unset($res_500, $pos_500);
				break;
			}
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_502 = \false; break; }
		$_502 = \true; break;
	}
	while(\false);
	if( $_502 === \true ) { return $this->finalise($result); }
	if( $_502 === \false) { return \false; }
}



}
