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
		'function', 'break', 'continue', 'while', 'try', 'catch', 'not', 'in'
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


/* RegexLiteral: "rx" core:StringLiteral */
protected $match_RegexLiteral_typestack = ['RegexLiteral'];
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_18 = \null;
	do {
		if (($subres = $this->literal('rx')) !== \false) { $result["text"] .= $subres; }
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


/* ComparisonOperatorWithWhitespace: "in"  | "not in" */
protected $match_ComparisonOperatorWithWhitespace_typestack = ['ComparisonOperatorWithWhitespace'];
function match_ComparisonOperatorWithWhitespace () {
	$matchrule = "ComparisonOperatorWithWhitespace"; $result = $this->construct($matchrule, $matchrule);
	$_186 = \null;
	do {
		$res_183 = $result;
		$pos_183 = $this->pos;
		if (($subres = $this->literal('in')) !== \false) {
			$result["text"] .= $subres;
			$_186 = \true; break;
		}
		$result = $res_183;
		$this->pos = $pos_183;
		if (($subres = $this->literal('not in')) !== \false) {
			$result["text"] .= $subres;
			$_186 = \true; break;
		}
		$result = $res_183;
		$this->pos = $pos_183;
		$_186 = \false; break;
	}
	while(\false);
	if( $_186 === \true ) { return $this->finalise($result); }
	if( $_186 === \false) { return \false; }
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
	$_198 = \null;
	do {
		$res_191 = $result;
		$pos_191 = $this->pos;
		$key = 'match_AnonymousFunction'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AnonymousFunction($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_198 = \true; break;
		}
		$result = $res_191;
		$this->pos = $pos_191;
		$_196 = \null;
		do {
			$res_193 = $result;
			$pos_193 = $this->pos;
			$key = 'match_Assignment'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Assignment($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_196 = \true; break;
			}
			$result = $res_193;
			$this->pos = $pos_193;
			$key = 'match_CondExpr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_CondExpr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_196 = \true; break;
			}
			$result = $res_193;
			$this->pos = $pos_193;
			$_196 = \false; break;
		}
		while(\false);
		if( $_196 === \true ) { $_198 = \true; break; }
		$result = $res_191;
		$this->pos = $pos_191;
		$_198 = \false; break;
	}
	while(\false);
	if( $_198 === \true ) { return $this->finalise($result); }
	if( $_198 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_205 = \null;
	do {
		$key = 'match_Mutable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Mutable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_205 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_205 = \false; break; }
		$key = 'match_AssignmentOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AssignmentOperator($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_205 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_205 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_205 = \false; break; }
		$_205 = \true; break;
	}
	while(\false);
	if( $_205 === \true ) { return $this->finalise($result); }
	if( $_205 === \false) { return \false; }
}


/* CondExpr: true:LogicalOr ( ] "if" __ "(" __ cond:Expression __ ")" __ "else" ] false:LogicalOr )? */
protected $match_CondExpr_typestack = ['CondExpr'];
function match_CondExpr ($stack = []) {
	$matchrule = "CondExpr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_222 = \null;
	do {
		$key = 'match_LogicalOr'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "true");
		}
		else { $_222 = \false; break; }
		$res_221 = $result;
		$pos_221 = $this->pos;
		$_220 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_220 = \false; break; }
			if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
			else { $_220 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_220 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_220 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_220 = \false; break; }
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "cond");
			}
			else { $_220 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_220 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_220 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_220 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_220 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_220 = \false; break; }
			$key = 'match_LogicalOr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "false");
			}
			else { $_220 = \false; break; }
			$_220 = \true; break;
		}
		while(\false);
		if( $_220 === \false) {
			$result = $res_221;
			$this->pos = $pos_221;
			unset($res_221, $pos_221);
		}
		$_222 = \true; break;
	}
	while(\false);
	if( $_222 === \true ) { return $this->finalise($result); }
	if( $_222 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_231 = \null;
	do {
		$key = 'match_LogicalAnd'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_231 = \false; break; }
		while (\true) {
			$res_230 = $result;
			$pos_230 = $this->pos;
			$_229 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_229 = \false; break; }
				$key = 'match_OrOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_OrOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_229 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_229 = \false; break; }
				$key = 'match_LogicalAnd'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_229 = \false; break; }
				$_229 = \true; break;
			}
			while(\false);
			if( $_229 === \false) {
				$result = $res_230;
				$this->pos = $pos_230;
				unset($res_230, $pos_230);
				break;
			}
		}
		$_231 = \true; break;
	}
	while(\false);
	if( $_231 === \true ) { return $this->finalise($result); }
	if( $_231 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_240 = \null;
	do {
		$key = 'match_Comparison'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_240 = \false; break; }
		while (\true) {
			$res_239 = $result;
			$pos_239 = $this->pos;
			$_238 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_238 = \false; break; }
				$key = 'match_AndOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AndOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_238 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_238 = \false; break; }
				$key = 'match_Comparison'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_238 = \false; break; }
				$_238 = \true; break;
			}
			while(\false);
			if( $_238 === \false) {
				$result = $res_239;
				$this->pos = $pos_239;
				unset($res_239, $pos_239);
				break;
			}
		}
		$_240 = \true; break;
	}
	while(\false);
	if( $_240 === \true ) { return $this->finalise($result); }
	if( $_240 === \false) { return \false; }
}


/* Comparison: operands:Addition ( ( __ ops:ComparisonOperator __ | ops: ] ComparisonOperatorWithWhitespace ] ) operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_261 = \null;
	do {
		$key = 'match_Addition'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Addition($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_261 = \false; break; }
		while (\true) {
			$res_260 = $result;
			$pos_260 = $this->pos;
			$_259 = \null;
			do {
				$_256 = \null;
				do {
					$_254 = \null;
					do {
						$res_243 = $result;
						$pos_243 = $this->pos;
						$_247 = \null;
						do {
							$key = 'match___'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match___($newStack));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_247 = \false; break; }
							$key = 'match_ComparisonOperator'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ComparisonOperator($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "ops");
							}
							else { $_247 = \false; break; }
							$key = 'match___'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match___($newStack));
							if ($subres !== \false) { $this->store($result, $subres); }
							else { $_247 = \false; break; }
							$_247 = \true; break;
						}
						while(\false);
						if( $_247 === \true ) { $_254 = \true; break; }
						$result = $res_243;
						$this->pos = $pos_243;
						$_252 = \null;
						do {
							if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
							else { $_252 = \false; break; }
							$key = 'match_ComparisonOperatorWithWhitespace'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ComparisonOperatorWithWhitespace($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "ops");
							}
							else { $_252 = \false; break; }
							if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
							else { $_252 = \false; break; }
							$_252 = \true; break;
						}
						while(\false);
						if( $_252 === \true ) { $_254 = \true; break; }
						$result = $res_243;
						$this->pos = $pos_243;
						$_254 = \false; break;
					}
					while(\false);
					if( $_254 === \false) { $_256 = \false; break; }
					$_256 = \true; break;
				}
				while(\false);
				if( $_256 === \false) { $_259 = \false; break; }
				$key = 'match_Addition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Addition($newStack));
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


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_270 = \null;
	do {
		$key = 'match_Multiplication'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_270 = \false; break; }
		while (\true) {
			$res_269 = $result;
			$pos_269 = $this->pos;
			$_268 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_268 = \false; break; }
				$key = 'match_AddOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AddOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_268 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_268 = \false; break; }
				$key = 'match_Multiplication'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_268 = \false; break; }
				$_268 = \true; break;
			}
			while(\false);
			if( $_268 === \false) {
				$result = $res_269;
				$this->pos = $pos_269;
				unset($res_269, $pos_269);
				break;
			}
		}
		$_270 = \true; break;
	}
	while(\false);
	if( $_270 === \true ) { return $this->finalise($result); }
	if( $_270 === \false) { return \false; }
}


/* Multiplication: operands:Exponentiation ( __ ops:MultiplyOperator __ operands:Exponentiation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_279 = \null;
	do {
		$key = 'match_Exponentiation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Exponentiation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_279 = \false; break; }
		while (\true) {
			$res_278 = $result;
			$pos_278 = $this->pos;
			$_277 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_277 = \false; break; }
				$key = 'match_MultiplyOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_MultiplyOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_277 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_277 = \false; break; }
				$key = 'match_Exponentiation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Exponentiation($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_277 = \false; break; }
				$_277 = \true; break;
			}
			while(\false);
			if( $_277 === \false) {
				$result = $res_278;
				$this->pos = $pos_278;
				unset($res_278, $pos_278);
				break;
			}
		}
		$_279 = \true; break;
	}
	while(\false);
	if( $_279 === \true ) { return $this->finalise($result); }
	if( $_279 === \false) { return \false; }
}


/* Exponentiation: operands:Negation ( __ ops:PowerOperator __ operands:Negation )* */
protected $match_Exponentiation_typestack = ['Exponentiation'];
function match_Exponentiation ($stack = []) {
	$matchrule = "Exponentiation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_288 = \null;
	do {
		$key = 'match_Negation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Negation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_288 = \false; break; }
		while (\true) {
			$res_287 = $result;
			$pos_287 = $this->pos;
			$_286 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_286 = \false; break; }
				$key = 'match_PowerOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_PowerOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_286 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_286 = \false; break; }
				$key = 'match_Negation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Negation($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_286 = \false; break; }
				$_286 = \true; break;
			}
			while(\false);
			if( $_286 === \false) {
				$result = $res_287;
				$this->pos = $pos_287;
				unset($res_287, $pos_287);
				break;
			}
		}
		$_288 = \true; break;
	}
	while(\false);
	if( $_288 === \true ) { return $this->finalise($result); }
	if( $_288 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_294 = \null;
	do {
		while (\true) {
			$res_292 = $result;
			$pos_292 = $this->pos;
			$_291 = \null;
			do {
				$key = 'match_NegationOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_NegationOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_291 = \false; break; }
				$_291 = \true; break;
			}
			while(\false);
			if( $_291 === \false) {
				$result = $res_292;
				$this->pos = $pos_292;
				unset($res_292, $pos_292);
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
		else { $_294 = \false; break; }
		$_294 = \true; break;
	}
	while(\false);
	if( $_294 === \true ) { return $this->finalise($result); }
	if( $_294 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_314 = \null;
	do {
		$res_296 = $result;
		$pos_296 = $this->pos;
		$_311 = \null;
		do {
			$_308 = \null;
			do {
				$_306 = \null;
				do {
					$res_297 = $result;
					$pos_297 = $this->pos;
					$_303 = \null;
					do {
						if (\substr($this->string, $this->pos, 1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_303 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_303 = \false; break; }
						$key = 'match_Expression'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_Expression($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_303 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_303 = \false; break; }
						if (\substr($this->string, $this->pos, 1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_303 = \false; break; }
						$_303 = \true; break;
					}
					while(\false);
					if( $_303 === \true ) { $_306 = \true; break; }
					$result = $res_297;
					$this->pos = $pos_297;
					$key = 'match_Value'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Value($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_306 = \true; break;
					}
					$result = $res_297;
					$this->pos = $pos_297;
					$_306 = \false; break;
				}
				while(\false);
				if( $_306 === \false) { $_308 = \false; break; }
				$_308 = \true; break;
			}
			while(\false);
			if( $_308 === \false) { $_311 = \false; break; }
			$res_310 = $result;
			$pos_310 = $this->pos;
			$key = 'match_Chain'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Chain($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_310;
				$this->pos = $pos_310;
				unset($res_310, $pos_310);
			}
			$_311 = \true; break;
		}
		while(\false);
		if( $_311 === \true ) { $_314 = \true; break; }
		$result = $res_296;
		$this->pos = $pos_296;
		$key = 'match_Value'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Value($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_314 = \true; break;
		}
		$result = $res_296;
		$this->pos = $pos_296;
		$_314 = \false; break;
	}
	while(\false);
	if( $_314 === \true ) { return $this->finalise($result); }
	if( $_314 === \false) { return \false; }
}


/* Chain: &/[\[\(\.]/ ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_329 = \null;
	do {
		$res_316 = $result;
		$pos_316 = $this->pos;
		if (($subres = $this->rx('/[\[\(\.]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_316;
			$this->pos = $pos_316;
		}
		else {
			$result = $res_316;
			$this->pos = $pos_316;
			$_329 = \false; break;
		}
		$_326 = \null;
		do {
			$_324 = \null;
			do {
				$res_317 = $result;
				$pos_317 = $this->pos;
				$key = 'match_Dereference'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Dereference($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_324 = \true; break;
				}
				$result = $res_317;
				$this->pos = $pos_317;
				$_322 = \null;
				do {
					$res_319 = $result;
					$pos_319 = $this->pos;
					$key = 'match_Invocation'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_322 = \true; break;
					}
					$result = $res_319;
					$this->pos = $pos_319;
					$key = 'match_ChainedFunction'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ChainedFunction($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_322 = \true; break;
					}
					$result = $res_319;
					$this->pos = $pos_319;
					$_322 = \false; break;
				}
				while(\false);
				if( $_322 === \true ) { $_324 = \true; break; }
				$result = $res_317;
				$this->pos = $pos_317;
				$_324 = \false; break;
			}
			while(\false);
			if( $_324 === \false) { $_326 = \false; break; }
			$_326 = \true; break;
		}
		while(\false);
		if( $_326 === \false) { $_329 = \false; break; }
		$res_328 = $result;
		$pos_328 = $this->pos;
		$key = 'match_Chain'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Chain($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_328;
			$this->pos = $pos_328;
			unset($res_328, $pos_328);
		}
		$_329 = \true; break;
	}
	while(\false);
	if( $_329 === \true ) { return $this->finalise($result); }
	if( $_329 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_336 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_336 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_336 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_336 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_336 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_336 = \false; break; }
		$_336 = \true; break;
	}
	while(\false);
	if( $_336 === \true ) { return $this->finalise($result); }
	if( $_336 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_343 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_343 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_343 = \false; break; }
		$res_340 = $result;
		$pos_340 = $this->pos;
		$key = 'match_ArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_340;
			$this->pos = $pos_340;
			unset($res_340, $pos_340);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_343 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_343 = \false; break; }
		$_343 = \true; break;
	}
	while(\false);
	if( $_343 === \true ) { return $this->finalise($result); }
	if( $_343 === \false) { return \false; }
}


/* ChainedFunction: "." fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_348 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '.') {
			$this->pos += 1;
			$result["text"] .= '.';
		}
		else { $_348 = \false; break; }
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_348 = \false; break; }
		$key = 'match_Invocation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_348 = \false; break; }
		$_348 = \true; break;
	}
	while(\false);
	if( $_348 === \true ) { return $this->finalise($result); }
	if( $_348 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_357 = \null;
	do {
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_357 = \false; break; }
		while (\true) {
			$res_356 = $result;
			$pos_356 = $this->pos;
			$_355 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_355 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_355 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_355 = \false; break; }
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_355 = \false; break; }
				$_355 = \true; break;
			}
			while(\false);
			if( $_355 === \false) {
				$result = $res_356;
				$this->pos = $pos_356;
				unset($res_356, $pos_356);
				break;
			}
		}
		$_357 = \true; break;
	}
	while(\false);
	if( $_357 === \true ) { return $this->finalise($result); }
	if( $_357 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_366 = \null;
	do {
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_366 = \false; break; }
		while (\true) {
			$res_365 = $result;
			$pos_365 = $this->pos;
			$_364 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_364 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_364 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_364 = \false; break; }
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_364 = \false; break; }
				$_364 = \true; break;
			}
			while(\false);
			if( $_364 === \false) {
				$result = $res_365;
				$this->pos = $pos_365;
				unset($res_365, $pos_365);
				break;
			}
		}
		$_366 = \true; break;
	}
	while(\false);
	if( $_366 === \true ) { return $this->finalise($result); }
	if( $_366 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_379 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_379 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_379 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_379 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_379 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_379 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_379 = \false; break; }
		$res_374 = $result;
		$pos_374 = $this->pos;
		$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_374;
			$this->pos = $pos_374;
			unset($res_374, $pos_374);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_379 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_379 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_379 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_379 = \false; break; }
		$_379 = \true; break;
	}
	while(\false);
	if( $_379 === \true ) { return $this->finalise($result); }
	if( $_379 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_400 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
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
		$_390 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_390 = \false; break; }
			$_390 = \true; break;
		}
		while(\false);
		if( $_390 === \false) { $_400 = \false; break; }
		$res_399 = $result;
		$pos_399 = $this->pos;
		$_398 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_398 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_398 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_398 = \false; break; }
			$_396 = \null;
			do {
				$key = 'match_Block'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Block($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_396 = \false; break; }
				$_396 = \true; break;
			}
			while(\false);
			if( $_396 === \false) { $_398 = \false; break; }
			$_398 = \true; break;
		}
		while(\false);
		if( $_398 === \false) {
			$result = $res_399;
			$this->pos = $pos_399;
			unset($res_399, $pos_399);
		}
		$_400 = \true; break;
	}
	while(\false);
	if( $_400 === \true ) { return $this->finalise($result); }
	if( $_400 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ ( key:VariableName __ ":" __ )? item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_423 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_423 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_423 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_423 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_423 = \false; break; }
		$res_411 = $result;
		$pos_411 = $this->pos;
		$_410 = \null;
		do {
			$key = 'match_VariableName'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "key");
			}
			else { $_410 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_410 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_410 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_410 = \false; break; }
			$_410 = \true; break;
		}
		while(\false);
		if( $_410 === \false) {
			$result = $res_411;
			$this->pos = $pos_411;
			unset($res_411, $pos_411);
		}
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_423 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_423 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_423 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_423 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_423 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_423 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_423 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_423 = \false; break; }
		$_421 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_421 = \false; break; }
			$_421 = \true; break;
		}
		while(\false);
		if( $_421 === \false) { $_423 = \false; break; }
		$_423 = \true; break;
	}
	while(\false);
	if( $_423 === \true ) { return $this->finalise($result); }
	if( $_423 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_436 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_436 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_436 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_436 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_436 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_436 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_436 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_436 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_436 = \false; break; }
		$_434 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_434 = \false; break; }
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


/* TryStatement: "try" __ main:Block __ "catch" __ onerror:Block */
protected $match_TryStatement_typestack = ['TryStatement'];
function match_TryStatement ($stack = []) {
	$matchrule = "TryStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_445 = \null;
	do {
		if (($subres = $this->literal('try')) !== \false) { $result["text"] .= $subres; }
		else { $_445 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_445 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "main");
		}
		else { $_445 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_445 = \false; break; }
		if (($subres = $this->literal('catch')) !== \false) { $result["text"] .= $subres; }
		else { $_445 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_445 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "onerror");
		}
		else { $_445 = \false; break; }
		$_445 = \true; break;
	}
	while(\false);
	if( $_445 === \true ) { return $this->finalise($result); }
	if( $_445 === \false) { return \false; }
}


/* CommandStatements: &/[rbc]/ ( skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement ) */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_459 = \null;
	do {
		$res_447 = $result;
		$pos_447 = $this->pos;
		if (($subres = $this->rx('/[rbc]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_447;
			$this->pos = $pos_447;
		}
		else {
			$result = $res_447;
			$this->pos = $pos_447;
			$_459 = \false; break;
		}
		$_457 = \null;
		do {
			$_455 = \null;
			do {
				$res_448 = $result;
				$pos_448 = $this->pos;
				$key = 'match_ReturnStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ReturnStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_455 = \true; break;
				}
				$result = $res_448;
				$this->pos = $pos_448;
				$_453 = \null;
				do {
					$res_450 = $result;
					$pos_450 = $this->pos;
					$key = 'match_BreakStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_BreakStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_453 = \true; break;
					}
					$result = $res_450;
					$this->pos = $pos_450;
					$key = 'match_ContinueStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ContinueStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_453 = \true; break;
					}
					$result = $res_450;
					$this->pos = $pos_450;
					$_453 = \false; break;
				}
				while(\false);
				if( $_453 === \true ) { $_455 = \true; break; }
				$result = $res_448;
				$this->pos = $pos_448;
				$_455 = \false; break;
			}
			while(\false);
			if( $_455 === \false) { $_457 = \false; break; }
			$_457 = \true; break;
		}
		while(\false);
		if( $_457 === \false) { $_459 = \false; break; }
		$_459 = \true; break;
	}
	while(\false);
	if( $_459 === \true ) { return $this->finalise($result); }
	if( $_459 === \false) { return \false; }
}


/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_473 = \null;
	do {
		$res_461 = $result;
		$pos_461 = $this->pos;
		$_467 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_467 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_467 = \false; break; }
			$res_466 = $result;
			$pos_466 = $this->pos;
			$_465 = \null;
			do {
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_465 = \false; break; }
				$_465 = \true; break;
			}
			while(\false);
			if( $_465 === \false) {
				$result = $res_466;
				$this->pos = $pos_466;
				unset($res_466, $pos_466);
			}
			$_467 = \true; break;
		}
		while(\false);
		if( $_467 === \true ) { $_473 = \true; break; }
		$result = $res_461;
		$this->pos = $pos_461;
		$_471 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_471 = \false; break; }
			$res_470 = $result;
			$pos_470 = $this->pos;
			$key = 'match_SEP'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_SEP($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_470;
				$this->pos = $pos_470;
			}
			else {
				$result = $res_470;
				$this->pos = $pos_470;
				$_471 = \false; break;
			}
			$_471 = \true; break;
		}
		while(\false);
		if( $_471 === \true ) { $_473 = \true; break; }
		$result = $res_461;
		$this->pos = $pos_461;
		$_473 = \false; break;
	}
	while(\false);
	if( $_473 === \true ) { return $this->finalise($result); }
	if( $_473 === \false) { return \false; }
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
	$_497 = \null;
	do {
		$res_477 = $result;
		$pos_477 = $this->pos;
		if (($subres = $this->rx('/[iwft]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_477;
			$this->pos = $pos_477;
		}
		else {
			$result = $res_477;
			$this->pos = $pos_477;
			$_497 = \false; break;
		}
		$_495 = \null;
		do {
			$_493 = \null;
			do {
				$res_478 = $result;
				$pos_478 = $this->pos;
				$key = 'match_IfStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_IfStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_493 = \true; break;
				}
				$result = $res_478;
				$this->pos = $pos_478;
				$_491 = \null;
				do {
					$res_480 = $result;
					$pos_480 = $this->pos;
					$key = 'match_WhileStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_WhileStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_491 = \true; break;
					}
					$result = $res_480;
					$this->pos = $pos_480;
					$_489 = \null;
					do {
						$res_482 = $result;
						$pos_482 = $this->pos;
						$key = 'match_FunctionDefinition'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_FunctionDefinition($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_489 = \true; break;
						}
						$result = $res_482;
						$this->pos = $pos_482;
						$_487 = \null;
						do {
							$res_484 = $result;
							$pos_484 = $this->pos;
							$key = 'match_ForStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_ForStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_487 = \true; break;
							}
							$result = $res_484;
							$this->pos = $pos_484;
							$key = 'match_TryStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_TryStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_487 = \true; break;
							}
							$result = $res_484;
							$this->pos = $pos_484;
							$_487 = \false; break;
						}
						while(\false);
						if( $_487 === \true ) { $_489 = \true; break; }
						$result = $res_482;
						$this->pos = $pos_482;
						$_489 = \false; break;
					}
					while(\false);
					if( $_489 === \true ) { $_491 = \true; break; }
					$result = $res_480;
					$this->pos = $pos_480;
					$_491 = \false; break;
				}
				while(\false);
				if( $_491 === \true ) { $_493 = \true; break; }
				$result = $res_478;
				$this->pos = $pos_478;
				$_493 = \false; break;
			}
			while(\false);
			if( $_493 === \false) { $_495 = \false; break; }
			$_495 = \true; break;
		}
		while(\false);
		if( $_495 === \false) { $_497 = \false; break; }
		$_497 = \true; break;
	}
	while(\false);
	if( $_497 === \true ) { return $this->finalise($result); }
	if( $_497 === \false) { return \false; }
}


/* Statement: !/[\s\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_511 = \null;
	do {
		$res_499 = $result;
		$pos_499 = $this->pos;
		if (($subres = $this->rx('/[\s\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_499;
			$this->pos = $pos_499;
			$_511 = \false; break;
		}
		else {
			$result = $res_499;
			$this->pos = $pos_499;
		}
		$_509 = \null;
		do {
			$_507 = \null;
			do {
				$res_500 = $result;
				$pos_500 = $this->pos;
				$key = 'match_BlockStatements'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BlockStatements($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_507 = \true; break;
				}
				$result = $res_500;
				$this->pos = $pos_500;
				$_505 = \null;
				do {
					$res_502 = $result;
					$pos_502 = $this->pos;
					$key = 'match_CommandStatements'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_CommandStatements($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_505 = \true; break;
					}
					$result = $res_502;
					$this->pos = $pos_502;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_505 = \true; break;
					}
					$result = $res_502;
					$this->pos = $pos_502;
					$_505 = \false; break;
				}
				while(\false);
				if( $_505 === \true ) { $_507 = \true; break; }
				$result = $res_500;
				$this->pos = $pos_500;
				$_507 = \false; break;
			}
			while(\false);
			if( $_507 === \false) { $_509 = \false; break; }
			$_509 = \true; break;
		}
		while(\false);
		if( $_509 === \false) { $_511 = \false; break; }
		$_511 = \true; break;
	}
	while(\false);
	if( $_511 === \true ) { return $this->finalise($result); }
	if( $_511 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_519 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_519 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_519 = \false; break; }
		$res_517 = $result;
		$pos_517 = $this->pos;
		$_516 = \null;
		do {
			$key = 'match_Program'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Program($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_516 = \false; break; }
			$_516 = \true; break;
		}
		while(\false);
		if( $_516 === \false) {
			$result = $res_517;
			$this->pos = $pos_517;
			unset($res_517, $pos_517);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_519 = \false; break; }
		$_519 = \true; break;
	}
	while(\false);
	if( $_519 === \true ) { return $this->finalise($result); }
	if( $_519 === \false) { return \false; }
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
	$_526 = \null;
	do {
		$res_523 = $result;
		$pos_523 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_526 = \true; break;
		}
		$result = $res_523;
		$this->pos = $pos_523;
		$key = 'match_NL'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NL($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_526 = \true; break;
		}
		$result = $res_523;
		$this->pos = $pos_523;
		$_526 = \false; break;
	}
	while(\false);
	if( $_526 === \true ) { return $this->finalise($result); }
	if( $_526 === \false) { return \false; }
}


/* Program: ( __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_535 = \null;
	do {
		while (\true) {
			$res_533 = $result;
			$pos_533 = $this->pos;
			$_532 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_532 = \false; break; }
				$res_529 = $result;
				$pos_529 = $this->pos;
				$key = 'match_Statement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Statement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_529;
					$this->pos = $pos_529;
					unset($res_529, $pos_529);
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$key = 'match_SEP'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_SEP($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_532 = \false; break; }
				$_532 = \true; break;
			}
			while(\false);
			if( $_532 === \false) {
				$result = $res_533;
				$this->pos = $pos_533;
				unset($res_533, $pos_533);
				break;
			}
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_535 = \false; break; }
		$_535 = \true; break;
	}
	while(\false);
	if( $_535 === \true ) { return $this->finalise($result); }
	if( $_535 === \false) { return \false; }
}




}
