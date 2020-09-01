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


/* PowerOperator: "**" */
protected $match_PowerOperator_typestack = ['PowerOperator'];
function match_PowerOperator () {
	$matchrule = "PowerOperator"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('**')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
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
	$_181 = \null;
	do {
		$res_162 = $result;
		$pos_162 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_181 = \true; break;
		}
		$result = $res_162;
		$this->pos = $pos_162;
		$_179 = \null;
		do {
			$res_164 = $result;
			$pos_164 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_179 = \true; break;
			}
			$result = $res_164;
			$this->pos = $pos_164;
			$_177 = \null;
			do {
				$res_166 = $result;
				$pos_166 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_177 = \true; break;
				}
				$result = $res_166;
				$this->pos = $pos_166;
				$_175 = \null;
				do {
					$res_168 = $result;
					$pos_168 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_175 = \true; break;
					}
					$result = $res_168;
					$this->pos = $pos_168;
					$_173 = \null;
					do {
						$res_170 = $result;
						$pos_170 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_173 = \true; break;
						}
						$result = $res_170;
						$this->pos = $pos_170;
						if (\substr($this->string, $this->pos, 1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_173 = \true; break;
						}
						$result = $res_170;
						$this->pos = $pos_170;
						$_173 = \false; break;
					}
					while(\false);
					if( $_173 === \true ) { $_175 = \true; break; }
					$result = $res_168;
					$this->pos = $pos_168;
					$_175 = \false; break;
				}
				while(\false);
				if( $_175 === \true ) { $_177 = \true; break; }
				$result = $res_166;
				$this->pos = $pos_166;
				$_177 = \false; break;
			}
			while(\false);
			if( $_177 === \true ) { $_179 = \true; break; }
			$result = $res_164;
			$this->pos = $pos_164;
			$_179 = \false; break;
		}
		while(\false);
		if( $_179 === \true ) { $_181 = \true; break; }
		$result = $res_162;
		$this->pos = $pos_162;
		$_181 = \false; break;
	}
	while(\false);
	if( $_181 === \true ) { return $this->finalise($result); }
	if( $_181 === \false) { return \false; }
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
	$_193 = \null;
	do {
		$res_186 = $result;
		$pos_186 = $this->pos;
		$key = 'match_AnonymousFunction'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AnonymousFunction($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_193 = \true; break;
		}
		$result = $res_186;
		$this->pos = $pos_186;
		$_191 = \null;
		do {
			$res_188 = $result;
			$pos_188 = $this->pos;
			$key = 'match_Assignment'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Assignment($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_191 = \true; break;
			}
			$result = $res_188;
			$this->pos = $pos_188;
			$key = 'match_CondExpr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_CondExpr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_191 = \true; break;
			}
			$result = $res_188;
			$this->pos = $pos_188;
			$_191 = \false; break;
		}
		while(\false);
		if( $_191 === \true ) { $_193 = \true; break; }
		$result = $res_186;
		$this->pos = $pos_186;
		$_193 = \false; break;
	}
	while(\false);
	if( $_193 === \true ) { return $this->finalise($result); }
	if( $_193 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_200 = \null;
	do {
		$key = 'match_Mutable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Mutable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_200 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_200 = \false; break; }
		$key = 'match_AssignmentOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AssignmentOperator($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_200 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_200 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_200 = \false; break; }
		$_200 = \true; break;
	}
	while(\false);
	if( $_200 === \true ) { return $this->finalise($result); }
	if( $_200 === \false) { return \false; }
}


/* CondExpr: true:LogicalOr ( ] "if" __ "(" __ cond:Expression __ ")" __ "else" ] false:LogicalOr )? */
protected $match_CondExpr_typestack = ['CondExpr'];
function match_CondExpr ($stack = []) {
	$matchrule = "CondExpr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_217 = \null;
	do {
		$key = 'match_LogicalOr'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "true");
		}
		else { $_217 = \false; break; }
		$res_216 = $result;
		$pos_216 = $this->pos;
		$_215 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_215 = \false; break; }
			if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
			else { $_215 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_215 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_215 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_215 = \false; break; }
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "cond");
			}
			else { $_215 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_215 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_215 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_215 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_215 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_215 = \false; break; }
			$key = 'match_LogicalOr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "false");
			}
			else { $_215 = \false; break; }
			$_215 = \true; break;
		}
		while(\false);
		if( $_215 === \false) {
			$result = $res_216;
			$this->pos = $pos_216;
			unset($res_216, $pos_216);
		}
		$_217 = \true; break;
	}
	while(\false);
	if( $_217 === \true ) { return $this->finalise($result); }
	if( $_217 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_226 = \null;
	do {
		$key = 'match_LogicalAnd'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_226 = \false; break; }
		while (\true) {
			$res_225 = $result;
			$pos_225 = $this->pos;
			$_224 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_224 = \false; break; }
				$key = 'match_OrOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_OrOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_224 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_224 = \false; break; }
				$key = 'match_LogicalAnd'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_224 = \false; break; }
				$_224 = \true; break;
			}
			while(\false);
			if( $_224 === \false) {
				$result = $res_225;
				$this->pos = $pos_225;
				unset($res_225, $pos_225);
				break;
			}
		}
		$_226 = \true; break;
	}
	while(\false);
	if( $_226 === \true ) { return $this->finalise($result); }
	if( $_226 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_235 = \null;
	do {
		$key = 'match_Comparison'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_235 = \false; break; }
		while (\true) {
			$res_234 = $result;
			$pos_234 = $this->pos;
			$_233 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_233 = \false; break; }
				$key = 'match_AndOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AndOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_233 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_233 = \false; break; }
				$key = 'match_Comparison'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_233 = \false; break; }
				$_233 = \true; break;
			}
			while(\false);
			if( $_233 === \false) {
				$result = $res_234;
				$this->pos = $pos_234;
				unset($res_234, $pos_234);
				break;
			}
		}
		$_235 = \true; break;
	}
	while(\false);
	if( $_235 === \true ) { return $this->finalise($result); }
	if( $_235 === \false) { return \false; }
}


/* Comparison: operands:Addition ( __ ops:ComparisonOperator __ operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_244 = \null;
	do {
		$key = 'match_Addition'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Addition($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_244 = \false; break; }
		while (\true) {
			$res_243 = $result;
			$pos_243 = $this->pos;
			$_242 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_242 = \false; break; }
				$key = 'match_ComparisonOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ComparisonOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_242 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_242 = \false; break; }
				$key = 'match_Addition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Addition($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_242 = \false; break; }
				$_242 = \true; break;
			}
			while(\false);
			if( $_242 === \false) {
				$result = $res_243;
				$this->pos = $pos_243;
				unset($res_243, $pos_243);
				break;
			}
		}
		$_244 = \true; break;
	}
	while(\false);
	if( $_244 === \true ) { return $this->finalise($result); }
	if( $_244 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_253 = \null;
	do {
		$key = 'match_Multiplication'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_253 = \false; break; }
		while (\true) {
			$res_252 = $result;
			$pos_252 = $this->pos;
			$_251 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_251 = \false; break; }
				$key = 'match_AddOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AddOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_251 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_251 = \false; break; }
				$key = 'match_Multiplication'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_251 = \false; break; }
				$_251 = \true; break;
			}
			while(\false);
			if( $_251 === \false) {
				$result = $res_252;
				$this->pos = $pos_252;
				unset($res_252, $pos_252);
				break;
			}
		}
		$_253 = \true; break;
	}
	while(\false);
	if( $_253 === \true ) { return $this->finalise($result); }
	if( $_253 === \false) { return \false; }
}


/* Multiplication: operands:Exponentiation ( __ ops:MultiplyOperator __ operands:Exponentiation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_262 = \null;
	do {
		$key = 'match_Exponentiation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Exponentiation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_262 = \false; break; }
		while (\true) {
			$res_261 = $result;
			$pos_261 = $this->pos;
			$_260 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_260 = \false; break; }
				$key = 'match_MultiplyOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_MultiplyOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_260 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_260 = \false; break; }
				$key = 'match_Exponentiation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Exponentiation($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_260 = \false; break; }
				$_260 = \true; break;
			}
			while(\false);
			if( $_260 === \false) {
				$result = $res_261;
				$this->pos = $pos_261;
				unset($res_261, $pos_261);
				break;
			}
		}
		$_262 = \true; break;
	}
	while(\false);
	if( $_262 === \true ) { return $this->finalise($result); }
	if( $_262 === \false) { return \false; }
}


/* Exponentiation: operands:Negation ( __ ops:PowerOperator __ operands:Negation )* */
protected $match_Exponentiation_typestack = ['Exponentiation'];
function match_Exponentiation ($stack = []) {
	$matchrule = "Exponentiation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_271 = \null;
	do {
		$key = 'match_Negation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Negation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_271 = \false; break; }
		while (\true) {
			$res_270 = $result;
			$pos_270 = $this->pos;
			$_269 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_269 = \false; break; }
				$key = 'match_PowerOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_PowerOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_269 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_269 = \false; break; }
				$key = 'match_Negation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Negation($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_269 = \false; break; }
				$_269 = \true; break;
			}
			while(\false);
			if( $_269 === \false) {
				$result = $res_270;
				$this->pos = $pos_270;
				unset($res_270, $pos_270);
				break;
			}
		}
		$_271 = \true; break;
	}
	while(\false);
	if( $_271 === \true ) { return $this->finalise($result); }
	if( $_271 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_277 = \null;
	do {
		while (\true) {
			$res_275 = $result;
			$pos_275 = $this->pos;
			$_274 = \null;
			do {
				$key = 'match_NegationOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_NegationOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_274 = \false; break; }
				$_274 = \true; break;
			}
			while(\false);
			if( $_274 === \false) {
				$result = $res_275;
				$this->pos = $pos_275;
				unset($res_275, $pos_275);
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
		else { $_277 = \false; break; }
		$_277 = \true; break;
	}
	while(\false);
	if( $_277 === \true ) { return $this->finalise($result); }
	if( $_277 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_297 = \null;
	do {
		$res_279 = $result;
		$pos_279 = $this->pos;
		$_294 = \null;
		do {
			$_291 = \null;
			do {
				$_289 = \null;
				do {
					$res_280 = $result;
					$pos_280 = $this->pos;
					$_286 = \null;
					do {
						if (\substr($this->string, $this->pos, 1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_286 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_286 = \false; break; }
						$key = 'match_Expression'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_Expression($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_286 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_286 = \false; break; }
						if (\substr($this->string, $this->pos, 1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_286 = \false; break; }
						$_286 = \true; break;
					}
					while(\false);
					if( $_286 === \true ) { $_289 = \true; break; }
					$result = $res_280;
					$this->pos = $pos_280;
					$key = 'match_Value'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Value($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_289 = \true; break;
					}
					$result = $res_280;
					$this->pos = $pos_280;
					$_289 = \false; break;
				}
				while(\false);
				if( $_289 === \false) { $_291 = \false; break; }
				$_291 = \true; break;
			}
			while(\false);
			if( $_291 === \false) { $_294 = \false; break; }
			$res_293 = $result;
			$pos_293 = $this->pos;
			$key = 'match_Chain'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Chain($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_293;
				$this->pos = $pos_293;
				unset($res_293, $pos_293);
			}
			$_294 = \true; break;
		}
		while(\false);
		if( $_294 === \true ) { $_297 = \true; break; }
		$result = $res_279;
		$this->pos = $pos_279;
		$key = 'match_Value'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Value($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_297 = \true; break;
		}
		$result = $res_279;
		$this->pos = $pos_279;
		$_297 = \false; break;
	}
	while(\false);
	if( $_297 === \true ) { return $this->finalise($result); }
	if( $_297 === \false) { return \false; }
}


/* Chain: &/[\[\(\.]/ ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_312 = \null;
	do {
		$res_299 = $result;
		$pos_299 = $this->pos;
		if (($subres = $this->rx('/[\[\(\.]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_299;
			$this->pos = $pos_299;
		}
		else {
			$result = $res_299;
			$this->pos = $pos_299;
			$_312 = \false; break;
		}
		$_309 = \null;
		do {
			$_307 = \null;
			do {
				$res_300 = $result;
				$pos_300 = $this->pos;
				$key = 'match_Dereference'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Dereference($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_307 = \true; break;
				}
				$result = $res_300;
				$this->pos = $pos_300;
				$_305 = \null;
				do {
					$res_302 = $result;
					$pos_302 = $this->pos;
					$key = 'match_Invocation'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_305 = \true; break;
					}
					$result = $res_302;
					$this->pos = $pos_302;
					$key = 'match_ChainedFunction'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ChainedFunction($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_305 = \true; break;
					}
					$result = $res_302;
					$this->pos = $pos_302;
					$_305 = \false; break;
				}
				while(\false);
				if( $_305 === \true ) { $_307 = \true; break; }
				$result = $res_300;
				$this->pos = $pos_300;
				$_307 = \false; break;
			}
			while(\false);
			if( $_307 === \false) { $_309 = \false; break; }
			$_309 = \true; break;
		}
		while(\false);
		if( $_309 === \false) { $_312 = \false; break; }
		$res_311 = $result;
		$pos_311 = $this->pos;
		$key = 'match_Chain'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Chain($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_311;
			$this->pos = $pos_311;
			unset($res_311, $pos_311);
		}
		$_312 = \true; break;
	}
	while(\false);
	if( $_312 === \true ) { return $this->finalise($result); }
	if( $_312 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_319 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_319 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_319 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_319 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_319 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_319 = \false; break; }
		$_319 = \true; break;
	}
	while(\false);
	if( $_319 === \true ) { return $this->finalise($result); }
	if( $_319 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_326 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_326 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_326 = \false; break; }
		$res_323 = $result;
		$pos_323 = $this->pos;
		$key = 'match_ArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_323;
			$this->pos = $pos_323;
			unset($res_323, $pos_323);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_326 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_326 = \false; break; }
		$_326 = \true; break;
	}
	while(\false);
	if( $_326 === \true ) { return $this->finalise($result); }
	if( $_326 === \false) { return \false; }
}


/* ChainedFunction: "." fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_331 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_331 = \false; break; }
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_331 = \false; break; }
		$key = 'match_Invocation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_331 = \false; break; }
		$_331 = \true; break;
	}
	while(\false);
	if( $_331 === \true ) { return $this->finalise($result); }
	if( $_331 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_340 = \null;
	do {
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_340 = \false; break; }
		while (\true) {
			$res_339 = $result;
			$pos_339 = $this->pos;
			$_338 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_338 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_338 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_338 = \false; break; }
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_338 = \false; break; }
				$_338 = \true; break;
			}
			while(\false);
			if( $_338 === \false) {
				$result = $res_339;
				$this->pos = $pos_339;
				unset($res_339, $pos_339);
				break;
			}
		}
		$_340 = \true; break;
	}
	while(\false);
	if( $_340 === \true ) { return $this->finalise($result); }
	if( $_340 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_349 = \null;
	do {
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_349 = \false; break; }
		while (\true) {
			$res_348 = $result;
			$pos_348 = $this->pos;
			$_347 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_347 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_347 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_347 = \false; break; }
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_347 = \false; break; }
				$_347 = \true; break;
			}
			while(\false);
			if( $_347 === \false) {
				$result = $res_348;
				$this->pos = $pos_348;
				unset($res_348, $pos_348);
				break;
			}
		}
		$_349 = \true; break;
	}
	while(\false);
	if( $_349 === \true ) { return $this->finalise($result); }
	if( $_349 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_362 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_362 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_362 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_362 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_362 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		$res_357 = $result;
		$pos_357 = $this->pos;
		$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_357;
			$this->pos = $pos_357;
			unset($res_357, $pos_357);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_362 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_362 = \false; break; }
		$_362 = \true; break;
	}
	while(\false);
	if( $_362 === \true ) { return $this->finalise($result); }
	if( $_362 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_383 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_383 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_383 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_383 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_383 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_383 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_383 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_383 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_383 = \false; break; }
		$_373 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_373 = \false; break; }
			$_373 = \true; break;
		}
		while(\false);
		if( $_373 === \false) { $_383 = \false; break; }
		$res_382 = $result;
		$pos_382 = $this->pos;
		$_381 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_381 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_381 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_381 = \false; break; }
			$_379 = \null;
			do {
				$key = 'match_Block'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Block($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_379 = \false; break; }
				$_379 = \true; break;
			}
			while(\false);
			if( $_379 === \false) { $_381 = \false; break; }
			$_381 = \true; break;
		}
		while(\false);
		if( $_381 === \false) {
			$result = $res_382;
			$this->pos = $pos_382;
			unset($res_382, $pos_382);
		}
		$_383 = \true; break;
	}
	while(\false);
	if( $_383 === \true ) { return $this->finalise($result); }
	if( $_383 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_400 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_400 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_400 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_400 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_400 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_400 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_400 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_400 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_400 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_400 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_400 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_400 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_400 = \false; break; }
		$_398 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_398 = \false; break; }
			$_398 = \true; break;
		}
		while(\false);
		if( $_398 === \false) { $_400 = \false; break; }
		$_400 = \true; break;
	}
	while(\false);
	if( $_400 === \true ) { return $this->finalise($result); }
	if( $_400 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_413 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_413 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_413 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_413 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_413 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_413 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_413 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_413 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_413 = \false; break; }
		$_411 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_411 = \false; break; }
			$_411 = \true; break;
		}
		while(\false);
		if( $_411 === \false) { $_413 = \false; break; }
		$_413 = \true; break;
	}
	while(\false);
	if( $_413 === \true ) { return $this->finalise($result); }
	if( $_413 === \false) { return \false; }
}


/* TryStatement: "try" __ main:Block __ "catch" __ onerror:Block */
protected $match_TryStatement_typestack = ['TryStatement'];
function match_TryStatement ($stack = []) {
	$matchrule = "TryStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_422 = \null;
	do {
		if (($subres = $this->literal('try')) !== \false) { $result["text"] .= $subres; }
		else { $_422 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_422 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "main");
		}
		else { $_422 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_422 = \false; break; }
		if (($subres = $this->literal('catch')) !== \false) { $result["text"] .= $subres; }
		else { $_422 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_422 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "onerror");
		}
		else { $_422 = \false; break; }
		$_422 = \true; break;
	}
	while(\false);
	if( $_422 === \true ) { return $this->finalise($result); }
	if( $_422 === \false) { return \false; }
}


/* CommandStatements: &/[rbc]/ ( skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement ) */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_436 = \null;
	do {
		$res_424 = $result;
		$pos_424 = $this->pos;
		if (($subres = $this->rx('/[rbc]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_424;
			$this->pos = $pos_424;
		}
		else {
			$result = $res_424;
			$this->pos = $pos_424;
			$_436 = \false; break;
		}
		$_434 = \null;
		do {
			$_432 = \null;
			do {
				$res_425 = $result;
				$pos_425 = $this->pos;
				$key = 'match_ReturnStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ReturnStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_432 = \true; break;
				}
				$result = $res_425;
				$this->pos = $pos_425;
				$_430 = \null;
				do {
					$res_427 = $result;
					$pos_427 = $this->pos;
					$key = 'match_BreakStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_BreakStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_430 = \true; break;
					}
					$result = $res_427;
					$this->pos = $pos_427;
					$key = 'match_ContinueStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ContinueStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_430 = \true; break;
					}
					$result = $res_427;
					$this->pos = $pos_427;
					$_430 = \false; break;
				}
				while(\false);
				if( $_430 === \true ) { $_432 = \true; break; }
				$result = $res_425;
				$this->pos = $pos_425;
				$_432 = \false; break;
			}
			while(\false);
			if( $_432 === \false) { $_434 = \false; break; }
			$_434 = \true; break;
		}
		while(\false);
		if( $_434 === \false) { $_436 = \false; break; }
		$_436 = \true; break;
	}
	while(\false);
	if( $_436 === \true ) { return $this->finalise($result); }
	if( $_436 === \false) { return \false; }
}


/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_450 = \null;
	do {
		$res_438 = $result;
		$pos_438 = $this->pos;
		$_444 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_444 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_444 = \false; break; }
			$res_443 = $result;
			$pos_443 = $this->pos;
			$_442 = \null;
			do {
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_442 = \false; break; }
				$_442 = \true; break;
			}
			while(\false);
			if( $_442 === \false) {
				$result = $res_443;
				$this->pos = $pos_443;
				unset($res_443, $pos_443);
			}
			$_444 = \true; break;
		}
		while(\false);
		if( $_444 === \true ) { $_450 = \true; break; }
		$result = $res_438;
		$this->pos = $pos_438;
		$_448 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_448 = \false; break; }
			$res_447 = $result;
			$pos_447 = $this->pos;
			$key = 'match_SEP'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_SEP($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_447;
				$this->pos = $pos_447;
			}
			else {
				$result = $res_447;
				$this->pos = $pos_447;
				$_448 = \false; break;
			}
			$_448 = \true; break;
		}
		while(\false);
		if( $_448 === \true ) { $_450 = \true; break; }
		$result = $res_438;
		$this->pos = $pos_438;
		$_450 = \false; break;
	}
	while(\false);
	if( $_450 === \true ) { return $this->finalise($result); }
	if( $_450 === \false) { return \false; }
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
	$_474 = \null;
	do {
		$res_454 = $result;
		$pos_454 = $this->pos;
		if (($subres = $this->rx('/[iwft]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_454;
			$this->pos = $pos_454;
		}
		else {
			$result = $res_454;
			$this->pos = $pos_454;
			$_474 = \false; break;
		}
		$_472 = \null;
		do {
			$_470 = \null;
			do {
				$res_455 = $result;
				$pos_455 = $this->pos;
				$key = 'match_IfStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_IfStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_470 = \true; break;
				}
				$result = $res_455;
				$this->pos = $pos_455;
				$_468 = \null;
				do {
					$res_457 = $result;
					$pos_457 = $this->pos;
					$key = 'match_WhileStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_WhileStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_468 = \true; break;
					}
					$result = $res_457;
					$this->pos = $pos_457;
					$_466 = \null;
					do {
						$res_459 = $result;
						$pos_459 = $this->pos;
						$key = 'match_FunctionDefinition'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_FunctionDefinition($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_466 = \true; break;
						}
						$result = $res_459;
						$this->pos = $pos_459;
						$_464 = \null;
						do {
							$res_461 = $result;
							$pos_461 = $this->pos;
							$key = 'match_ForStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ForStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_464 = \true; break;
							}
							$result = $res_461;
							$this->pos = $pos_461;
							$key = 'match_TryStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_TryStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_464 = \true; break;
							}
							$result = $res_461;
							$this->pos = $pos_461;
							$_464 = \false; break;
						}
						while(\false);
						if( $_464 === \true ) { $_466 = \true; break; }
						$result = $res_459;
						$this->pos = $pos_459;
						$_466 = \false; break;
					}
					while(\false);
					if( $_466 === \true ) { $_468 = \true; break; }
					$result = $res_457;
					$this->pos = $pos_457;
					$_468 = \false; break;
				}
				while(\false);
				if( $_468 === \true ) { $_470 = \true; break; }
				$result = $res_455;
				$this->pos = $pos_455;
				$_470 = \false; break;
			}
			while(\false);
			if( $_470 === \false) { $_472 = \false; break; }
			$_472 = \true; break;
		}
		while(\false);
		if( $_472 === \false) { $_474 = \false; break; }
		$_474 = \true; break;
	}
	while(\false);
	if( $_474 === \true ) { return $this->finalise($result); }
	if( $_474 === \false) { return \false; }
}


/* Statement: !/[\s\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_488 = \null;
	do {
		$res_476 = $result;
		$pos_476 = $this->pos;
		if (($subres = $this->rx('/[\s\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_476;
			$this->pos = $pos_476;
			$_488 = \false; break;
		}
		else {
			$result = $res_476;
			$this->pos = $pos_476;
		}
		$_486 = \null;
		do {
			$_484 = \null;
			do {
				$res_477 = $result;
				$pos_477 = $this->pos;
				$key = 'match_BlockStatements'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BlockStatements($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_484 = \true; break;
				}
				$result = $res_477;
				$this->pos = $pos_477;
				$_482 = \null;
				do {
					$res_479 = $result;
					$pos_479 = $this->pos;
					$key = 'match_CommandStatements'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_CommandStatements($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_482 = \true; break;
					}
					$result = $res_479;
					$this->pos = $pos_479;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_482 = \true; break;
					}
					$result = $res_479;
					$this->pos = $pos_479;
					$_482 = \false; break;
				}
				while(\false);
				if( $_482 === \true ) { $_484 = \true; break; }
				$result = $res_477;
				$this->pos = $pos_477;
				$_484 = \false; break;
			}
			while(\false);
			if( $_484 === \false) { $_486 = \false; break; }
			$_486 = \true; break;
		}
		while(\false);
		if( $_486 === \false) { $_488 = \false; break; }
		$_488 = \true; break;
	}
	while(\false);
	if( $_488 === \true ) { return $this->finalise($result); }
	if( $_488 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_496 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_496 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_496 = \false; break; }
		$res_494 = $result;
		$pos_494 = $this->pos;
		$_493 = \null;
		do {
			$key = 'match_Program'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Program($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_493 = \false; break; }
			$_493 = \true; break;
		}
		while(\false);
		if( $_493 === \false) {
			$result = $res_494;
			$this->pos = $pos_494;
			unset($res_494, $pos_494);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_496 = \false; break; }
		$_496 = \true; break;
	}
	while(\false);
	if( $_496 === \true ) { return $this->finalise($result); }
	if( $_496 === \false) { return \false; }
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
	$_503 = \null;
	do {
		$res_500 = $result;
		$pos_500 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_503 = \true; break;
		}
		$result = $res_500;
		$this->pos = $pos_500;
		$key = 'match_NL'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NL($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_503 = \true; break;
		}
		$result = $res_500;
		$this->pos = $pos_500;
		$_503 = \false; break;
	}
	while(\false);
	if( $_503 === \true ) { return $this->finalise($result); }
	if( $_503 === \false) { return \false; }
}


/* Program: ( __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_512 = \null;
	do {
		while (\true) {
			$res_510 = $result;
			$pos_510 = $this->pos;
			$_509 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_509 = \false; break; }
				$res_506 = $result;
				$pos_506 = $this->pos;
				$key = 'match_Statement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Statement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_506;
					$this->pos = $pos_506;
					unset($res_506, $pos_506);
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$key = 'match_SEP'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_SEP($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_509 = \false; break; }
				$_509 = \true; break;
			}
			while(\false);
			if( $_509 === \false) {
				$result = $res_510;
				$this->pos = $pos_510;
				unset($res_510, $pos_510);
				break;
			}
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_512 = \false; break; }
		$_512 = \true; break;
	}
	while(\false);
	if( $_512 === \true ) { return $this->finalise($result); }
	if( $_512 === \false) { return \false; }
}




}
