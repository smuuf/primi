<?php

namespace Smuuf\Primi;

use \hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

	// Add these properties so PHPStan doesn't complain about undefined properties.

	/** @var int */
	public $pos;

	/** @var string */
	public $string;

/* StringLiteral: / ("[^"\\]*(\\.[^"\\]*)*")|('[^'\\]*(\\.[^'\\]*)*') /s */
protected $match_StringLiteral_typestack = ['StringLiteral'];
function match_StringLiteral ($stack = []) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ ("[^"\\\\]*(\\\\.[^"\\\\]*)*")|(\'[^\'\\\\]*(\\\\.[^\'\\\\]*)*\') /s')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NumberLiteral: /-?\d[\d_]*(\.[\d_]+)?/ */
protected $match_NumberLiteral_typestack = ['NumberLiteral'];
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/-?\d[\d_]*(\.[\d_]+)?/')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = ['BoolLiteral'];
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule);
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
	while(false);
	if( $_5 === \true ) { return $this->finalise($result); }
	if( $_5 === \false) { return \false; }
}


/* NullLiteral: "null" */
protected $match_NullLiteral_typestack = ['NullLiteral'];
function match_NullLiteral ($stack = []) {
	$matchrule = "NullLiteral"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('null')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* RegexLiteral: "r" core:StringLiteral */
protected $match_RegexLiteral_typestack = ['RegexLiteral'];
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_10 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === 'r') {
			$this->pos += 1;
			$result["text"] .= 'r';
		}
		else { $_10 = \false; break; }
		$key = 'match_StringLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_StringLiteral($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_10 = \false; break; }
		$_10 = \true; break;
	}
	while(false);
	if( $_10 === \true ) { return $this->finalise($result); }
	if( $_10 === \false) { return \false; }
}


/* RangeLiteral: left:RangeBoundary > ".." ( step:RangeBoundary ".." )? > right:RangeBoundary */
protected $match_RangeLiteral_typestack = ['RangeLiteral'];
function match_RangeLiteral ($stack = []) {
	$matchrule = "RangeLiteral"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_21 = \null;
	do {
		$key = 'match_RangeBoundary'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_RangeBoundary($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_21 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		if (($subres = $this->literal('..')) !== \false) { $result["text"] .= $subres; }
		else { $_21 = \false; break; }
		$res_18 = $result;
		$pos_18 = $this->pos;
		$_17 = \null;
		do {
			$key = 'match_RangeBoundary'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_RangeBoundary($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "step");
			}
			else { $_17 = \false; break; }
			if (($subres = $this->literal('..')) !== \false) { $result["text"] .= $subres; }
			else { $_17 = \false; break; }
			$_17 = \true; break;
		}
		while(false);
		if( $_17 === \false) {
			$result = $res_18;
			$this->pos = $pos_18;
			unset($res_18, $pos_18);
		}
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		$key = 'match_RangeBoundary'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_RangeBoundary($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_21 = \false; break; }
		$_21 = \true; break;
	}
	while(false);
	if( $_21 === \true ) { return $this->finalise($result); }
	if( $_21 === \false) { return \false; }
}


/* RangeBoundary: skip:NumberLiteral | skip:Variable */
protected $match_RangeBoundary_typestack = ['RangeBoundary'];
function match_RangeBoundary ($stack = []) {
	$matchrule = "RangeBoundary"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_26 = \null;
	do {
		$res_23 = $result;
		$pos_23 = $this->pos;
		$key = 'match_NumberLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NumberLiteral($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_26 = \true; break;
		}
		$result = $res_23;
		$this->pos = $pos_23;
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_26 = \true; break;
		}
		$result = $res_23;
		$this->pos = $pos_23;
		$_26 = \false; break;
	}
	while(false);
	if( $_26 === \true ) { return $this->finalise($result); }
	if( $_26 === \false) { return \false; }
}


/* Nothing: "" */
protected $match_Nothing_typestack = ['Nothing'];
function match_Nothing ($stack = []) {
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
	$_44 = \null;
	do {
		$res_29 = $result;
		$pos_29 = $this->pos;
		$key = 'match_NumberLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NumberLiteral($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_44 = \true; break;
		}
		$result = $res_29;
		$this->pos = $pos_29;
		$_42 = \null;
		do {
			$res_31 = $result;
			$pos_31 = $this->pos;
			$key = 'match_StringLiteral'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_StringLiteral($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_42 = \true; break;
			}
			$result = $res_31;
			$this->pos = $pos_31;
			$_40 = \null;
			do {
				$res_33 = $result;
				$pos_33 = $this->pos;
				$key = 'match_BoolLiteral'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BoolLiteral($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_40 = \true; break;
				}
				$result = $res_33;
				$this->pos = $pos_33;
				$_38 = \null;
				do {
					$res_35 = $result;
					$pos_35 = $this->pos;
					$key = 'match_NullLiteral'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_NullLiteral($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_38 = \true; break;
					}
					$result = $res_35;
					$this->pos = $pos_35;
					$key = 'match_RegexLiteral'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_RegexLiteral($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_38 = \true; break;
					}
					$result = $res_35;
					$this->pos = $pos_35;
					$_38 = \false; break;
				}
				while(false);
				if( $_38 === \true ) { $_40 = \true; break; }
				$result = $res_33;
				$this->pos = $pos_33;
				$_40 = \false; break;
			}
			while(false);
			if( $_40 === \true ) { $_42 = \true; break; }
			$result = $res_31;
			$this->pos = $pos_31;
			$_42 = \false; break;
		}
		while(false);
		if( $_42 === \true ) { $_44 = \true; break; }
		$result = $res_29;
		$this->pos = $pos_29;
		$_44 = \false; break;
	}
	while(false);
	if( $_44 === \true ) { return $this->finalise($result); }
	if( $_44 === \false) { return \false; }
}


/* VariableName: / ([a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = ['VariableName'];
function match_VariableName ($stack = []) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ ([a-zA-Z_][a-zA-Z0-9_]*) /')) !== \false) {
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
	$_71 = \null;
	do {
		$res_48 = $result;
		$pos_48 = $this->pos;
		$_58 = \null;
		do {
			if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
			else { $_58 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_58 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			$res_53 = $result;
			$pos_53 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_53;
				$this->pos = $pos_53;
				unset($res_53, $pos_53);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_58 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_58 = \false; break; }
			$_58 = \true; break;
		}
		while(false);
		if( $_58 === \true ) { $_71 = \true; break; }
		$result = $res_48;
		$this->pos = $pos_48;
		$_69 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_69 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			$res_62 = $result;
			$pos_62 = $this->pos;
			$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_62;
				$this->pos = $pos_62;
				unset($res_62, $pos_62);
			}
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_69 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			if (($subres = $this->literal('=>')) !== \false) { $result["text"] .= $subres; }
			else { $_69 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_69 = \false; break; }
			$_69 = \true; break;
		}
		while(false);
		if( $_69 === \true ) { $_71 = \true; break; }
		$result = $res_48;
		$this->pos = $pos_48;
		$_71 = \false; break;
	}
	while(false);
	if( $_71 === \true ) { return $this->finalise($result); }
	if( $_71 === \false) { return \false; }
}


/* ArrayItem: ( __ key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = ['ArrayItem'];
function match_ArrayItem ($stack = []) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_81 = \null;
	do {
		$res_78 = $result;
		$pos_78 = $this->pos;
		$_77 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_77 = \false; break; }
			$key = 'match_Expression'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Expression($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "key");
			}
			else { $_77 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_77 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_77 = \false; break; }
			$_77 = \true; break;
		}
		while(false);
		if( $_77 === \false) {
			$result = $res_78;
			$this->pos = $pos_78;
			unset($res_78, $pos_78);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_81 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "value");
		}
		else { $_81 = \false; break; }
		$_81 = \true; break;
	}
	while(false);
	if( $_81 === \true ) { return $this->finalise($result); }
	if( $_81 === \false) { return \false; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ ( "," __ )? "]" */
protected $match_ArrayDefinition_typestack = ['ArrayDefinition'];
function match_ArrayDefinition ($stack = []) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_100 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_100 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_100 = \false; break; }
		$res_93 = $result;
		$pos_93 = $this->pos;
		$_92 = \null;
		do {
			$key = 'match_ArrayItem'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_ArrayItem($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_92 = \false; break; }
			while (\true) {
				$res_91 = $result;
				$pos_91 = $this->pos;
				$_90 = \null;
				do {
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_90 = \false; break; }
					if (\substr($this->string, $this->pos, 1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_90 = \false; break; }
					$key = 'match___'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match___($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_90 = \false; break; }
					$key = 'match_ArrayItem'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ArrayItem($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_90 = \false; break; }
					$_90 = \true; break;
				}
				while(false);
				if( $_90 === \false) {
					$result = $res_91;
					$this->pos = $pos_91;
					unset($res_91, $pos_91);
					break;
				}
			}
			$_92 = \true; break;
		}
		while(false);
		if( $_92 === \false) {
			$result = $res_93;
			$this->pos = $pos_93;
			unset($res_93, $pos_93);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_100 = \false; break; }
		$res_98 = $result;
		$pos_98 = $this->pos;
		$_97 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_97 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_97 = \false; break; }
			$_97 = \true; break;
		}
		while(false);
		if( $_97 === \false) {
			$result = $res_98;
			$this->pos = $pos_98;
			unset($res_98, $pos_98);
		}
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_100 = \false; break; }
		$_100 = \true; break;
	}
	while(false);
	if( $_100 === \true ) { return $this->finalise($result); }
	if( $_100 === \false) { return \false; }
}


/* Value: skip:RangeLiteral | skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = ['Value'];
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_113 = \null;
	do {
		$res_102 = $result;
		$pos_102 = $this->pos;
		$key = 'match_RangeLiteral'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_RangeLiteral($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_113 = \true; break;
		}
		$result = $res_102;
		$this->pos = $pos_102;
		$_111 = \null;
		do {
			$res_104 = $result;
			$pos_104 = $this->pos;
			$key = 'match_Literal'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Literal($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_111 = \true; break;
			}
			$result = $res_104;
			$this->pos = $pos_104;
			$_109 = \null;
			do {
				$res_106 = $result;
				$pos_106 = $this->pos;
				$key = 'match_Variable'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Variable($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_109 = \true; break;
				}
				$result = $res_106;
				$this->pos = $pos_106;
				$key = 'match_ArrayDefinition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ArrayDefinition($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_109 = \true; break;
				}
				$result = $res_106;
				$this->pos = $pos_106;
				$_109 = \false; break;
			}
			while(false);
			if( $_109 === \true ) { $_111 = \true; break; }
			$result = $res_104;
			$this->pos = $pos_104;
			$_111 = \false; break;
		}
		while(false);
		if( $_111 === \true ) { $_113 = \true; break; }
		$result = $res_102;
		$this->pos = $pos_102;
		$_113 = \false; break;
	}
	while(false);
	if( $_113 === \true ) { return $this->finalise($result); }
	if( $_113 === \false) { return \false; }
}


/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = ['VariableVector'];
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_117 = \null;
	do {
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_117 = \false; break; }
		$key = 'match_Vector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Vector($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else { $_117 = \false; break; }
		$_117 = \true; break;
	}
	while(false);
	if( $_117 === \true ) { return $this->finalise($result); }
	if( $_117 === \false) { return \false; }
}


/* Vector: ( "[" __ ( arrayKey:Expression | arrayKey:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = ['Vector'];
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_133 = \null;
	do {
		$_130 = \null;
		do {
			if (\substr($this->string, $this->pos, 1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_130 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_130 = \false; break; }
			$_126 = \null;
			do {
				$_124 = \null;
				do {
					$res_121 = $result;
					$pos_121 = $this->pos;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "arrayKey");
						$_124 = \true; break;
					}
					$result = $res_121;
					$this->pos = $pos_121;
					$key = 'match_Nothing'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Nothing($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "arrayKey");
						$_124 = \true; break;
					}
					$result = $res_121;
					$this->pos = $pos_121;
					$_124 = \false; break;
				}
				while(false);
				if( $_124 === \false) { $_126 = \false; break; }
				$_126 = \true; break;
			}
			while(false);
			if( $_126 === \false) { $_130 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_130 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_130 = \false; break; }
			$_130 = \true; break;
		}
		while(false);
		if( $_130 === \false) { $_133 = \false; break; }
		$res_132 = $result;
		$pos_132 = $this->pos;
		$key = 'match_Vector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Vector($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else {
			$result = $res_132;
			$this->pos = $pos_132;
			unset($res_132, $pos_132);
		}
		$_133 = \true; break;
	}
	while(false);
	if( $_133 === \true ) { return $this->finalise($result); }
	if( $_133 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = ['Mutable'];
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_138 = \null;
	do {
		$res_135 = $result;
		$pos_135 = $this->pos;
		$key = 'match_VariableVector'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableVector($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_138 = \true; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_138 = \true; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		$_138 = \false; break;
	}
	while(false);
	if( $_138 === \true ) { return $this->finalise($result); }
	if( $_138 === \false) { return \false; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = ['ObjectResolutionOperator'];
function match_ObjectResolutionOperator ($stack = []) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule);
	if (\substr($this->string, $this->pos, 1) === '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = ['AddOperator'];
function match_AddOperator ($stack = []) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule);
	$_144 = \null;
	do {
		$res_141 = $result;
		$pos_141 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_144 = \true; break;
		}
		$result = $res_141;
		$this->pos = $pos_141;
		if (\substr($this->string, $this->pos, 1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_144 = \true; break;
		}
		$result = $res_141;
		$this->pos = $pos_141;
		$_144 = \false; break;
	}
	while(false);
	if( $_144 === \true ) { return $this->finalise($result); }
	if( $_144 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = ['MultiplyOperator'];
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule);
	$_149 = \null;
	do {
		$res_146 = $result;
		$pos_146 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_149 = \true; break;
		}
		$result = $res_146;
		$this->pos = $pos_146;
		if (\substr($this->string, $this->pos, 1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_149 = \true; break;
		}
		$result = $res_146;
		$this->pos = $pos_146;
		$_149 = \false; break;
	}
	while(false);
	if( $_149 === \true ) { return $this->finalise($result); }
	if( $_149 === \false) { return \false; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = ['AssignmentOperator'];
function match_AssignmentOperator ($stack = []) {
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
function match_ComparisonOperator ($stack = []) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule);
	$_171 = \null;
	do {
		$res_152 = $result;
		$pos_152 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_171 = \true; break;
		}
		$result = $res_152;
		$this->pos = $pos_152;
		$_169 = \null;
		do {
			$res_154 = $result;
			$pos_154 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_169 = \true; break;
			}
			$result = $res_154;
			$this->pos = $pos_154;
			$_167 = \null;
			do {
				$res_156 = $result;
				$pos_156 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_167 = \true; break;
				}
				$result = $res_156;
				$this->pos = $pos_156;
				$_165 = \null;
				do {
					$res_158 = $result;
					$pos_158 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_165 = \true; break;
					}
					$result = $res_158;
					$this->pos = $pos_158;
					$_163 = \null;
					do {
						$res_160 = $result;
						$pos_160 = $this->pos;
						if (\substr($this->string, $this->pos, 1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_163 = \true; break;
						}
						$result = $res_160;
						$this->pos = $pos_160;
						if (\substr($this->string, $this->pos, 1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_163 = \true; break;
						}
						$result = $res_160;
						$this->pos = $pos_160;
						$_163 = \false; break;
					}
					while(false);
					if( $_163 === \true ) { $_165 = \true; break; }
					$result = $res_158;
					$this->pos = $pos_158;
					$_165 = \false; break;
				}
				while(false);
				if( $_165 === \true ) { $_167 = \true; break; }
				$result = $res_156;
				$this->pos = $pos_156;
				$_167 = \false; break;
			}
			while(false);
			if( $_167 === \true ) { $_169 = \true; break; }
			$result = $res_154;
			$this->pos = $pos_154;
			$_169 = \false; break;
		}
		while(false);
		if( $_169 === \true ) { $_171 = \true; break; }
		$result = $res_152;
		$this->pos = $pos_152;
		$_171 = \false; break;
	}
	while(false);
	if( $_171 === \true ) { return $this->finalise($result); }
	if( $_171 === \false) { return \false; }
}


/* AndOperator: "and" */
protected $match_AndOperator_typestack = ['AndOperator'];
function match_AndOperator ($stack = []) {
	$matchrule = "AndOperator"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('and')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* OrOperator: "or" */
protected $match_OrOperator_typestack = ['OrOperator'];
function match_OrOperator ($stack = []) {
	$matchrule = "OrOperator"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('or')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NegationOperator: "!" */
protected $match_NegationOperator_typestack = ['NegationOperator'];
function match_NegationOperator ($stack = []) {
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
	$_183 = \null;
	do {
		$res_176 = $result;
		$pos_176 = $this->pos;
		$key = 'match_AnonymousFunction'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AnonymousFunction($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_183 = \true; break;
		}
		$result = $res_176;
		$this->pos = $pos_176;
		$_181 = \null;
		do {
			$res_178 = $result;
			$pos_178 = $this->pos;
			$key = 'match_Assignment'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Assignment($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_181 = \true; break;
			}
			$result = $res_178;
			$this->pos = $pos_178;
			$key = 'match_CondExpr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_CondExpr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_181 = \true; break;
			}
			$result = $res_178;
			$this->pos = $pos_178;
			$_181 = \false; break;
		}
		while(false);
		if( $_181 === \true ) { $_183 = \true; break; }
		$result = $res_176;
		$this->pos = $pos_176;
		$_183 = \false; break;
	}
	while(false);
	if( $_183 === \true ) { return $this->finalise($result); }
	if( $_183 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_190 = \null;
	do {
		$key = 'match_Mutable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Mutable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_190 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_190 = \false; break; }
		$key = 'match_AssignmentOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_AssignmentOperator($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_190 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_190 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_190 = \false; break; }
		$_190 = \true; break;
	}
	while(false);
	if( $_190 === \true ) { return $this->finalise($result); }
	if( $_190 === \false) { return \false; }
}


/* CondExpr: true:LogicalOr ( ] "if" __ "(" __ cond:Expression __ ")" __ "else" ] false:LogicalOr )? */
protected $match_CondExpr_typestack = ['CondExpr'];
function match_CondExpr ($stack = []) {
	$matchrule = "CondExpr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_207 = \null;
	do {
		$key = 'match_LogicalOr'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "true");
		}
		else { $_207 = \false; break; }
		$res_206 = $result;
		$pos_206 = $this->pos;
		$_205 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_205 = \false; break; }
			if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
			else { $_205 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_205 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
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
				$this->store($result, $subres, "cond");
			}
			else { $_205 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_205 = \false; break; }
			if (\substr($this->string, $this->pos, 1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_205 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_205 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_205 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_205 = \false; break; }
			$key = 'match_LogicalOr'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_LogicalOr($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "false");
			}
			else { $_205 = \false; break; }
			$_205 = \true; break;
		}
		while(false);
		if( $_205 === \false) {
			$result = $res_206;
			$this->pos = $pos_206;
			unset($res_206, $pos_206);
		}
		$_207 = \true; break;
	}
	while(false);
	if( $_207 === \true ) { return $this->finalise($result); }
	if( $_207 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_216 = \null;
	do {
		$key = 'match_LogicalAnd'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_216 = \false; break; }
		while (\true) {
			$res_215 = $result;
			$pos_215 = $this->pos;
			$_214 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_214 = \false; break; }
				$key = 'match_OrOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_OrOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_214 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_214 = \false; break; }
				$key = 'match_LogicalAnd'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_LogicalAnd($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_214 = \false; break; }
				$_214 = \true; break;
			}
			while(false);
			if( $_214 === \false) {
				$result = $res_215;
				$this->pos = $pos_215;
				unset($res_215, $pos_215);
				break;
			}
		}
		$_216 = \true; break;
	}
	while(false);
	if( $_216 === \true ) { return $this->finalise($result); }
	if( $_216 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_225 = \null;
	do {
		$key = 'match_Comparison'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
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
				$key = 'match_AndOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AndOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_223 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_223 = \false; break; }
				$key = 'match_Comparison'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Comparison($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_223 = \false; break; }
				$_223 = \true; break;
			}
			while(false);
			if( $_223 === \false) {
				$result = $res_224;
				$this->pos = $pos_224;
				unset($res_224, $pos_224);
				break;
			}
		}
		$_225 = \true; break;
	}
	while(false);
	if( $_225 === \true ) { return $this->finalise($result); }
	if( $_225 === \false) { return \false; }
}


/* Comparison: operands:Addition ( __ ops:ComparisonOperator __ operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_234 = \null;
	do {
		$key = 'match_Addition'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Addition($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_234 = \false; break; }
		while (\true) {
			$res_233 = $result;
			$pos_233 = $this->pos;
			$_232 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_232 = \false; break; }
				$key = 'match_ComparisonOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ComparisonOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_232 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_232 = \false; break; }
				$key = 'match_Addition'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Addition($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_232 = \false; break; }
				$_232 = \true; break;
			}
			while(false);
			if( $_232 === \false) {
				$result = $res_233;
				$this->pos = $pos_233;
				unset($res_233, $pos_233);
				break;
			}
		}
		$_234 = \true; break;
	}
	while(false);
	if( $_234 === \true ) { return $this->finalise($result); }
	if( $_234 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_243 = \null;
	do {
		$key = 'match_Multiplication'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
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
				$key = 'match_AddOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_AddOperator($newStack));
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
				$key = 'match_Multiplication'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Multiplication($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_241 = \false; break; }
				$_241 = \true; break;
			}
			while(false);
			if( $_241 === \false) {
				$result = $res_242;
				$this->pos = $pos_242;
				unset($res_242, $pos_242);
				break;
			}
		}
		$_243 = \true; break;
	}
	while(false);
	if( $_243 === \true ) { return $this->finalise($result); }
	if( $_243 === \false) { return \false; }
}


/* Multiplication: operands:Negation ( __ ops:MultiplyOperator __ operands:Negation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_252 = \null;
	do {
		$key = 'match_Negation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Negation($newStack));
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
				$key = 'match_MultiplyOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_MultiplyOperator($newStack));
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
				$key = 'match_Negation'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Negation($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_250 = \false; break; }
				$_250 = \true; break;
			}
			while(false);
			if( $_250 === \false) {
				$result = $res_251;
				$this->pos = $pos_251;
				unset($res_251, $pos_251);
				break;
			}
		}
		$_252 = \true; break;
	}
	while(false);
	if( $_252 === \true ) { return $this->finalise($result); }
	if( $_252 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_258 = \null;
	do {
		while (\true) {
			$res_256 = $result;
			$pos_256 = $this->pos;
			$_255 = \null;
			do {
				$key = 'match_NegationOperator'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_NegationOperator($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_255 = \false; break; }
				$_255 = \true; break;
			}
			while(false);
			if( $_255 === \false) {
				$result = $res_256;
				$this->pos = $pos_256;
				unset($res_256, $pos_256);
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
		else { $_258 = \false; break; }
		$_258 = \true; break;
	}
	while(false);
	if( $_258 === \true ) { return $this->finalise($result); }
	if( $_258 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_278 = \null;
	do {
		$res_260 = $result;
		$pos_260 = $this->pos;
		$_275 = \null;
		do {
			$_272 = \null;
			do {
				$_270 = \null;
				do {
					$res_261 = $result;
					$pos_261 = $this->pos;
					$_267 = \null;
					do {
						if (\substr($this->string, $this->pos, 1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_267 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_267 = \false; break; }
						$key = 'match_Expression'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_Expression($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_267 = \false; break; }
						$key = 'match___'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match___($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_267 = \false; break; }
						if (\substr($this->string, $this->pos, 1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_267 = \false; break; }
						$_267 = \true; break;
					}
					while(false);
					if( $_267 === \true ) { $_270 = \true; break; }
					$result = $res_261;
					$this->pos = $pos_261;
					$key = 'match_Value'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Value($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_270 = \true; break;
					}
					$result = $res_261;
					$this->pos = $pos_261;
					$_270 = \false; break;
				}
				while(false);
				if( $_270 === \false) { $_272 = \false; break; }
				$_272 = \true; break;
			}
			while(false);
			if( $_272 === \false) { $_275 = \false; break; }
			$res_274 = $result;
			$pos_274 = $this->pos;
			$key = 'match_Chain'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Chain($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_274;
				$this->pos = $pos_274;
				unset($res_274, $pos_274);
			}
			$_275 = \true; break;
		}
		while(false);
		if( $_275 === \true ) { $_278 = \true; break; }
		$result = $res_260;
		$this->pos = $pos_260;
		$key = 'match_Value'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Value($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_278 = \true; break;
		}
		$result = $res_260;
		$this->pos = $pos_260;
		$_278 = \false; break;
	}
	while(false);
	if( $_278 === \true ) { return $this->finalise($result); }
	if( $_278 === \false) { return \false; }
}


/* Chain: ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_292 = \null;
	do {
		$_289 = \null;
		do {
			$_287 = \null;
			do {
				$res_280 = $result;
				$pos_280 = $this->pos;
				$key = 'match_Dereference'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Dereference($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_287 = \true; break;
				}
				$result = $res_280;
				$this->pos = $pos_280;
				$_285 = \null;
				do {
					$res_282 = $result;
					$pos_282 = $this->pos;
					$key = 'match_Invocation'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_285 = \true; break;
					}
					$result = $res_282;
					$this->pos = $pos_282;
					$key = 'match_ChainedFunction'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ChainedFunction($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_285 = \true; break;
					}
					$result = $res_282;
					$this->pos = $pos_282;
					$_285 = \false; break;
				}
				while(false);
				if( $_285 === \true ) { $_287 = \true; break; }
				$result = $res_280;
				$this->pos = $pos_280;
				$_287 = \false; break;
			}
			while(false);
			if( $_287 === \false) { $_289 = \false; break; }
			$_289 = \true; break;
		}
		while(false);
		if( $_289 === \false) { $_292 = \false; break; }
		$res_291 = $result;
		$pos_291 = $this->pos;
		$key = 'match_Chain'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Chain($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_291;
			$this->pos = $pos_291;
			unset($res_291, $pos_291);
		}
		$_292 = \true; break;
	}
	while(false);
	if( $_292 === \true ) { return $this->finalise($result); }
	if( $_292 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_299 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_299 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_299 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_299 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_299 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_299 = \false; break; }
		$_299 = \true; break;
	}
	while(false);
	if( $_299 === \true ) { return $this->finalise($result); }
	if( $_299 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_306 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_306 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_306 = \false; break; }
		$res_303 = $result;
		$pos_303 = $this->pos;
		$key = 'match_ArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_303;
			$this->pos = $pos_303;
			unset($res_303, $pos_303);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_306 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_306 = \false; break; }
		$_306 = \true; break;
	}
	while(false);
	if( $_306 === \true ) { return $this->finalise($result); }
	if( $_306 === \false) { return \false; }
}


/* ChainedFunction: ObjectResolutionOperator fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_311 = \null;
	do {
		$key = 'match_ObjectResolutionOperator'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_ObjectResolutionOperator($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_311 = \false; break; }
		$key = 'match_Variable'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Variable($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_311 = \false; break; }
		$key = 'match_Invocation'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Invocation($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_311 = \false; break; }
		$_311 = \true; break;
	}
	while(false);
	if( $_311 === \true ) { return $this->finalise($result); }
	if( $_311 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_320 = \null;
	do {
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_320 = \false; break; }
		while (\true) {
			$res_319 = $result;
			$pos_319 = $this->pos;
			$_318 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_318 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_318 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_318 = \false; break; }
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_318 = \false; break; }
				$_318 = \true; break;
			}
			while(false);
			if( $_318 === \false) {
				$result = $res_319;
				$this->pos = $pos_319;
				unset($res_319, $pos_319);
				break;
			}
		}
		$_320 = \true; break;
	}
	while(false);
	if( $_320 === \true ) { return $this->finalise($result); }
	if( $_320 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_329 = \null;
	do {
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_329 = \false; break; }
		while (\true) {
			$res_328 = $result;
			$pos_328 = $this->pos;
			$_327 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_327 = \false; break; }
				if (\substr($this->string, $this->pos, 1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_327 = \false; break; }
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_327 = \false; break; }
				$key = 'match_VariableName'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_327 = \false; break; }
				$_327 = \true; break;
			}
			while(false);
			if( $_327 === \false) {
				$result = $res_328;
				$this->pos = $pos_328;
				unset($res_328, $pos_328);
				break;
			}
		}
		$_329 = \true; break;
	}
	while(false);
	if( $_329 === \true ) { return $this->finalise($result); }
	if( $_329 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_342 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_342 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_342 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_342 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_342 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_342 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_342 = \false; break; }
		$res_337 = $result;
		$pos_337 = $this->pos;
		$key = 'match_FunctionDefinitionArgumentList'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_FunctionDefinitionArgumentList($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_337;
			$this->pos = $pos_337;
			unset($res_337, $pos_337);
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_342 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_342 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_342 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_342 = \false; break; }
		$_342 = \true; break;
	}
	while(false);
	if( $_342 === \true ) { return $this->finalise($result); }
	if( $_342 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_363 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_363 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_363 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_363 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_363 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_363 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_363 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_363 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_363 = \false; break; }
		$_353 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_353 = \false; break; }
			$_353 = \true; break;
		}
		while(false);
		if( $_353 === \false) { $_363 = \false; break; }
		$res_362 = $result;
		$pos_362 = $this->pos;
		$_361 = \null;
		do {
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_361 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_361 = \false; break; }
			$key = 'match___'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match___($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_361 = \false; break; }
			$_359 = \null;
			do {
				$key = 'match_Block'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Block($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_359 = \false; break; }
				$_359 = \true; break;
			}
			while(false);
			if( $_359 === \false) { $_361 = \false; break; }
			$_361 = \true; break;
		}
		while(false);
		if( $_361 === \false) {
			$result = $res_362;
			$this->pos = $pos_362;
			unset($res_362, $pos_362);
		}
		$_363 = \true; break;
	}
	while(false);
	if( $_363 === \true ) { return $this->finalise($result); }
	if( $_363 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_380 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_380 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_380 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_380 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_380 = \false; break; }
		$key = 'match_VariableName'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_VariableName($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_380 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_380 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_380 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_380 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_380 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_380 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_380 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_380 = \false; break; }
		$_378 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_378 = \false; break; }
			$_378 = \true; break;
		}
		while(false);
		if( $_378 === \false) { $_380 = \false; break; }
		$_380 = \true; break;
	}
	while(false);
	if( $_380 === \true ) { return $this->finalise($result); }
	if( $_380 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_393 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_393 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_393 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_393 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_393 = \false; break; }
		$key = 'match_Expression'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Expression($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_393 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_393 = \false; break; }
		if (\substr($this->string, $this->pos, 1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_393 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_393 = \false; break; }
		$_391 = \null;
		do {
			$key = 'match_Block'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Block($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_391 = \false; break; }
			$_391 = \true; break;
		}
		while(false);
		if( $_391 === \false) { $_393 = \false; break; }
		$_393 = \true; break;
	}
	while(false);
	if( $_393 === \true ) { return $this->finalise($result); }
	if( $_393 === \false) { return \false; }
}


/* TryStatement: "try" __ main:Block __ "on error" __ onerror:Block */
protected $match_TryStatement_typestack = ['TryStatement'];
function match_TryStatement ($stack = []) {
	$matchrule = "TryStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_402 = \null;
	do {
		if (($subres = $this->literal('try')) !== \false) { $result["text"] .= $subres; }
		else { $_402 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_402 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "main");
		}
		else { $_402 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_402 = \false; break; }
		if (($subres = $this->literal('on error')) !== \false) { $result["text"] .= $subres; }
		else { $_402 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_402 = \false; break; }
		$key = 'match_Block'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_Block($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "onerror");
		}
		else { $_402 = \false; break; }
		$_402 = \true; break;
	}
	while(false);
	if( $_402 === \true ) { return $this->finalise($result); }
	if( $_402 === \false) { return \false; }
}


/* CommandStatements: &/[rbc]/ ( skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement ) */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_416 = \null;
	do {
		$res_404 = $result;
		$pos_404 = $this->pos;
		if (($subres = $this->rx('/[rbc]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_404;
			$this->pos = $pos_404;
		}
		else {
			$result = $res_404;
			$this->pos = $pos_404;
			$_416 = \false; break;
		}
		$_414 = \null;
		do {
			$_412 = \null;
			do {
				$res_405 = $result;
				$pos_405 = $this->pos;
				$key = 'match_ReturnStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_ReturnStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_412 = \true; break;
				}
				$result = $res_405;
				$this->pos = $pos_405;
				$_410 = \null;
				do {
					$res_407 = $result;
					$pos_407 = $this->pos;
					$key = 'match_BreakStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_BreakStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_410 = \true; break;
					}
					$result = $res_407;
					$this->pos = $pos_407;
					$key = 'match_ContinueStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_ContinueStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_410 = \true; break;
					}
					$result = $res_407;
					$this->pos = $pos_407;
					$_410 = \false; break;
				}
				while(false);
				if( $_410 === \true ) { $_412 = \true; break; }
				$result = $res_405;
				$this->pos = $pos_405;
				$_412 = \false; break;
			}
			while(false);
			if( $_412 === \false) { $_414 = \false; break; }
			$_414 = \true; break;
		}
		while(false);
		if( $_414 === \false) { $_416 = \false; break; }
		$_416 = \true; break;
	}
	while(false);
	if( $_416 === \true ) { return $this->finalise($result); }
	if( $_416 === \false) { return \false; }
}


/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_430 = \null;
	do {
		$res_418 = $result;
		$pos_418 = $this->pos;
		$_424 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_424 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_424 = \false; break; }
			$res_423 = $result;
			$pos_423 = $this->pos;
			$_422 = \null;
			do {
				$key = 'match_Expression'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Expression($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_422 = \false; break; }
				$_422 = \true; break;
			}
			while(false);
			if( $_422 === \false) {
				$result = $res_423;
				$this->pos = $pos_423;
				unset($res_423, $pos_423);
			}
			$_424 = \true; break;
		}
		while(false);
		if( $_424 === \true ) { $_430 = \true; break; }
		$result = $res_418;
		$this->pos = $pos_418;
		$_428 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_428 = \false; break; }
			$res_427 = $result;
			$pos_427 = $this->pos;
			$key = 'match_SEP'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_SEP($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_427;
				$this->pos = $pos_427;
			}
			else {
				$result = $res_427;
				$this->pos = $pos_427;
				$_428 = \false; break;
			}
			$_428 = \true; break;
		}
		while(false);
		if( $_428 === \true ) { $_430 = \true; break; }
		$result = $res_418;
		$this->pos = $pos_418;
		$_430 = \false; break;
	}
	while(false);
	if( $_430 === \true ) { return $this->finalise($result); }
	if( $_430 === \false) { return \false; }
}


/* BreakStatement: "break" */
protected $match_BreakStatement_typestack = ['BreakStatement'];
function match_BreakStatement ($stack = []) {
	$matchrule = "BreakStatement"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('break')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ContinueStatement: "continue" */
protected $match_ContinueStatement_typestack = ['ContinueStatement'];
function match_ContinueStatement ($stack = []) {
	$matchrule = "ContinueStatement"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->literal('continue')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BlockStatements: &/[iwft]/ ( skip:IfStatement | skip:WhileStatement | skip:ForStatement | skip:FunctionDefinition | skip:TryStatement) */
protected $match_BlockStatements_typestack = ['BlockStatements'];
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_454 = \null;
	do {
		$res_434 = $result;
		$pos_434 = $this->pos;
		if (($subres = $this->rx('/[iwft]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_434;
			$this->pos = $pos_434;
		}
		else {
			$result = $res_434;
			$this->pos = $pos_434;
			$_454 = \false; break;
		}
		$_452 = \null;
		do {
			$_450 = \null;
			do {
				$res_435 = $result;
				$pos_435 = $this->pos;
				$key = 'match_IfStatement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_IfStatement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_450 = \true; break;
				}
				$result = $res_435;
				$this->pos = $pos_435;
				$_448 = \null;
				do {
					$res_437 = $result;
					$pos_437 = $this->pos;
					$key = 'match_WhileStatement'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_WhileStatement($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_448 = \true; break;
					}
					$result = $res_437;
					$this->pos = $pos_437;
					$_446 = \null;
					do {
						$res_439 = $result;
						$pos_439 = $this->pos;
						$key = 'match_ForStatement'; $pos = $this->pos;
						$subres = $this->packhas($key, $pos)
							? $this->packread($key, $pos)
							: $this->packwrite($key, $pos, $this->match_ForStatement($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_446 = \true; break;
						}
						$result = $res_439;
						$this->pos = $pos_439;
						$_444 = \null;
						do {
							$res_441 = $result;
							$pos_441 = $this->pos;
							$key = 'match_FunctionDefinition'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_FunctionDefinition($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_444 = \true; break;
							}
							$result = $res_441;
							$this->pos = $pos_441;
							$key = 'match_TryStatement'; $pos = $this->pos;
							$subres = $this->packhas($key, $pos)
								? $this->packread($key, $pos)
								: $this->packwrite($key, $pos, $this->match_TryStatement($newStack));
							if ($subres !== \false) {
								$this->store($result, $subres, "skip");
								$_444 = \true; break;
							}
							$result = $res_441;
							$this->pos = $pos_441;
							$_444 = \false; break;
						}
						while(false);
						if( $_444 === \true ) { $_446 = \true; break; }
						$result = $res_439;
						$this->pos = $pos_439;
						$_446 = \false; break;
					}
					while(false);
					if( $_446 === \true ) { $_448 = \true; break; }
					$result = $res_437;
					$this->pos = $pos_437;
					$_448 = \false; break;
				}
				while(false);
				if( $_448 === \true ) { $_450 = \true; break; }
				$result = $res_435;
				$this->pos = $pos_435;
				$_450 = \false; break;
			}
			while(false);
			if( $_450 === \false) { $_452 = \false; break; }
			$_452 = \true; break;
		}
		while(false);
		if( $_452 === \false) { $_454 = \false; break; }
		$_454 = \true; break;
	}
	while(false);
	if( $_454 === \true ) { return $this->finalise($result); }
	if( $_454 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_468 = \null;
	do {
		$res_456 = $result;
		$pos_456 = $this->pos;
		if (($subres = $this->rx('/[\s\{\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_456;
			$this->pos = $pos_456;
			$_468 = \false; break;
		}
		else {
			$result = $res_456;
			$this->pos = $pos_456;
		}
		$_466 = \null;
		do {
			$_464 = \null;
			do {
				$res_457 = $result;
				$pos_457 = $this->pos;
				$key = 'match_BlockStatements'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_BlockStatements($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_464 = \true; break;
				}
				$result = $res_457;
				$this->pos = $pos_457;
				$_462 = \null;
				do {
					$res_459 = $result;
					$pos_459 = $this->pos;
					$key = 'match_CommandStatements'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_CommandStatements($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_462 = \true; break;
					}
					$result = $res_459;
					$this->pos = $pos_459;
					$key = 'match_Expression'; $pos = $this->pos;
					$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos, $this->match_Expression($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_462 = \true; break;
					}
					$result = $res_459;
					$this->pos = $pos_459;
					$_462 = \false; break;
				}
				while(false);
				if( $_462 === \true ) { $_464 = \true; break; }
				$result = $res_457;
				$this->pos = $pos_457;
				$_464 = \false; break;
			}
			while(false);
			if( $_464 === \false) { $_466 = \false; break; }
			$_466 = \true; break;
		}
		while(false);
		if( $_466 === \false) { $_468 = \false; break; }
		$_468 = \true; break;
	}
	while(false);
	if( $_468 === \true ) { return $this->finalise($result); }
	if( $_468 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_476 = \null;
	do {
		if (\substr($this->string, $this->pos, 1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_476 = \false; break; }
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_476 = \false; break; }
		$res_474 = $result;
		$pos_474 = $this->pos;
		$_473 = \null;
		do {
			$key = 'match_Program'; $pos = $this->pos;
			$subres = $this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->match_Program($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_473 = \false; break; }
			$_473 = \true; break;
		}
		while(false);
		if( $_473 === \false) {
			$result = $res_474;
			$this->pos = $pos_474;
			unset($res_474, $pos_474);
		}
		if (\substr($this->string, $this->pos, 1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_476 = \false; break; }
		$_476 = \true; break;
	}
	while(false);
	if( $_476 === \true ) { return $this->finalise($result); }
	if( $_476 === \false) { return \false; }
}


/* __: / [\s\n]*(?:\/\/[^\n]*)? / */
protected $match____typestack = ['__'];
function match___ ($stack = []) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule);
	if (($subres = $this->rx('/ [\s\n]*(?:\/\/[^\n]*)? /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = ['NL'];
function match_NL ($stack = []) {
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
	$_483 = \null;
	do {
		$res_480 = $result;
		$pos_480 = $this->pos;
		if (\substr($this->string, $this->pos, 1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_483 = \true; break;
		}
		$result = $res_480;
		$this->pos = $pos_480;
		$key = 'match_NL'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match_NL($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_483 = \true; break;
		}
		$result = $res_480;
		$this->pos = $pos_480;
		$_483 = \false; break;
	}
	while(false);
	if( $_483 === \true ) { return $this->finalise($result); }
	if( $_483 === \false) { return \false; }
}


/* Program: ( __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule); $newStack = \array_merge($stack, [$result]);
	$_492 = \null;
	do {
		while (\true) {
			$res_490 = $result;
			$pos_490 = $this->pos;
			$_489 = \null;
			do {
				$key = 'match___'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match___($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_489 = \false; break; }
				$res_486 = $result;
				$pos_486 = $this->pos;
				$key = 'match_Statement'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_Statement($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_486;
					$this->pos = $pos_486;
					unset($res_486, $pos_486);
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$key = 'match_SEP'; $pos = $this->pos;
				$subres = $this->packhas($key, $pos)
					? $this->packread($key, $pos)
					: $this->packwrite($key, $pos, $this->match_SEP($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_489 = \false; break; }
				$_489 = \true; break;
			}
			while(false);
			if( $_489 === \false) {
				$result = $res_490;
				$this->pos = $pos_490;
				unset($res_490, $pos_490);
				break;
			}
		}
		$key = 'match___'; $pos = $this->pos;
		$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->match___($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_492 = \false; break; }
		$_492 = \true; break;
	}
	while(false);
	if( $_492 === \true ) { return $this->finalise($result); }
	if( $_492 === \false) { return \false; }
}




}
