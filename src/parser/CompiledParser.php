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
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ ("[^"\\\\]*(\\\\.[^"\\\\]*)*")|(\'[^\'\\\\]*(\\\\.[^\'\\\\]*)*\') /s')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = ['NumberLiteral'];
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/-?\d+(\.\d+)?/')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = ['BoolLiteral'];
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
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
	while(0);
	if( $_5 === \true ) { return $this->finalise($result); }
	if( $_5 === \false) { return \false; }
}


/* NullLiteral: "null" */
protected $match_NullLiteral_typestack = ['NullLiteral'];
function match_NullLiteral ($stack = []) {
	$matchrule = "NullLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('null')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* RegexLiteral: "r" core:StringLiteral */
protected $match_RegexLiteral_typestack = ['RegexLiteral'];
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_10 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === 'r') {
			$this->pos += 1;
			$result["text"] .= 'r';
		}
		else { $_10 = \false; break; }
		$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_10 = \false; break; }
		$_10 = \true; break;
	}
	while(0);
	if( $_10 === \true ) { return $this->finalise($result); }
	if( $_10 === \false) { return \false; }
}


/* RangeLiteral: left:RangeBoundary > ".." ( step:RangeBoundary ".." )? > right:RangeBoundary */
protected $match_RangeLiteral_typestack = ['RangeLiteral'];
function match_RangeLiteral ($stack = []) {
	$matchrule = "RangeLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_21 = \null;
	do {
		$matcher = 'match_'.'RangeBoundary'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
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
			$matcher = 'match_'.'RangeBoundary'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "step");
			}
			else { $_17 = \false; break; }
			if (($subres = $this->literal('..')) !== \false) { $result["text"] .= $subres; }
			else { $_17 = \false; break; }
			$_17 = \true; break;
		}
		while(0);
		if( $_17 === \false) {
			$result = $res_18;
			$this->pos = $pos_18;
			unset( $res_18 );
			unset( $pos_18 );
		}
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		$matcher = 'match_'.'RangeBoundary'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_21 = \false; break; }
		$_21 = \true; break;
	}
	while(0);
	if( $_21 === \true ) { return $this->finalise($result); }
	if( $_21 === \false) { return \false; }
}


/* RangeBoundary: skip:NumberLiteral | skip:Variable */
protected $match_RangeBoundary_typestack = ['RangeBoundary'];
function match_RangeBoundary ($stack = []) {
	$matchrule = "RangeBoundary"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_26 = \null;
	do {
		$res_23 = $result;
		$pos_23 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_26 = \true; break;
		}
		$result = $res_23;
		$this->pos = $pos_23;
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_26 = \true; break;
		}
		$result = $res_23;
		$this->pos = $pos_23;
		$_26 = \false; break;
	}
	while(0);
	if( $_26 === \true ) { return $this->finalise($result); }
	if( $_26 === \false) { return \false; }
}


/* Nothing: "" */
protected $match_Nothing_typestack = ['Nothing'];
function match_Nothing ($stack = []) {
	$matchrule = "Nothing"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:NullLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = ['Literal'];
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_44 = \null;
	do {
		$res_29 = $result;
		$pos_29 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
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
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
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
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
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
					$matcher = 'match_'.'NullLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_38 = \true; break;
					}
					$result = $res_35;
					$this->pos = $pos_35;
					$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_38 = \true; break;
					}
					$result = $res_35;
					$this->pos = $pos_35;
					$_38 = \false; break;
				}
				while(0);
				if( $_38 === \true ) { $_40 = \true; break; }
				$result = $res_33;
				$this->pos = $pos_33;
				$_40 = \false; break;
			}
			while(0);
			if( $_40 === \true ) { $_42 = \true; break; }
			$result = $res_31;
			$this->pos = $pos_31;
			$_42 = \false; break;
		}
		while(0);
		if( $_42 === \true ) { $_44 = \true; break; }
		$result = $res_29;
		$this->pos = $pos_29;
		$_44 = \false; break;
	}
	while(0);
	if( $_44 === \true ) { return $this->finalise($result); }
	if( $_44 === \false) { return \false; }
}


/* VariableName: / ([a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = ['VariableName'];
function match_VariableName ($stack = []) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ ([a-zA-Z_][a-zA-Z0-9_]*) /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Variable: core:VariableName */
protected $match_Variable_typestack = ['Variable'];
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
	$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
	if ($subres !== \false) {
		$this->store($result, $subres, "core");
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AnonymousFunction: "function" __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ params:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = ['AnonymousFunction'];
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_71 = \null;
	do {
		$res_48 = $result;
		$pos_48 = $this->pos;
		$_58 = \null;
		do {
			if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
			else { $_58 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_58 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			$res_53 = $result;
			$pos_53 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_53;
				$this->pos = $pos_53;
				unset( $res_53 );
				unset( $pos_53 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_58 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_58 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_58 = \false; break; }
			$_58 = \true; break;
		}
		while(0);
		if( $_58 === \true ) { $_71 = \true; break; }
		$result = $res_48;
		$this->pos = $pos_48;
		$_69 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_69 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			$res_62 = $result;
			$pos_62 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "params");
			}
			else {
				$result = $res_62;
				$this->pos = $pos_62;
				unset( $res_62 );
				unset( $pos_62 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_69 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			if (($subres = $this->literal('=>')) !== \false) { $result["text"] .= $subres; }
			else { $_69 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_69 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "body");
			}
			else { $_69 = \false; break; }
			$_69 = \true; break;
		}
		while(0);
		if( $_69 === \true ) { $_71 = \true; break; }
		$result = $res_48;
		$this->pos = $pos_48;
		$_71 = \false; break;
	}
	while(0);
	if( $_71 === \true ) { return $this->finalise($result); }
	if( $_71 === \false) { return \false; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = ['ArrayItem'];
function match_ArrayItem ($stack = []) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_80 = \null;
	do {
		$res_77 = $result;
		$pos_77 = $this->pos;
		$_76 = \null;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "key");
			}
			else { $_76 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_76 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_76 = \false; break; }
			$_76 = \true; break;
		}
		while(0);
		if( $_76 === \false) {
			$result = $res_77;
			$this->pos = $pos_77;
			unset( $res_77 );
			unset( $pos_77 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_80 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "value");
		}
		else { $_80 = \false; break; }
		$_80 = \true; break;
	}
	while(0);
	if( $_80 === \true ) { return $this->finalise($result); }
	if( $_80 === \false) { return \false; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ ( "," __ )? "]" */
protected $match_ArrayDefinition_typestack = ['ArrayDefinition'];
function match_ArrayDefinition ($stack = []) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_99 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_99 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_99 = \false; break; }
		$res_92 = $result;
		$pos_92 = $this->pos;
		$_91 = \null;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_91 = \false; break; }
			while (\true) {
				$res_90 = $result;
				$pos_90 = $this->pos;
				$_89 = \null;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_89 = \false; break; }
					if (\substr($this->string,$this->pos,1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_89 = \false; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_89 = \false; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_89 = \false; break; }
					$_89 = \true; break;
				}
				while(0);
				if( $_89 === \false) {
					$result = $res_90;
					$this->pos = $pos_90;
					unset( $res_90 );
					unset( $pos_90 );
					break;
				}
			}
			$_91 = \true; break;
		}
		while(0);
		if( $_91 === \false) {
			$result = $res_92;
			$this->pos = $pos_92;
			unset( $res_92 );
			unset( $pos_92 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_99 = \false; break; }
		$res_97 = $result;
		$pos_97 = $this->pos;
		$_96 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_96 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_96 = \false; break; }
			$_96 = \true; break;
		}
		while(0);
		if( $_96 === \false) {
			$result = $res_97;
			$this->pos = $pos_97;
			unset( $res_97 );
			unset( $pos_97 );
		}
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_99 = \false; break; }
		$_99 = \true; break;
	}
	while(0);
	if( $_99 === \true ) { return $this->finalise($result); }
	if( $_99 === \false) { return \false; }
}


/* ListItem: skip:Expression */
protected $match_ListItem_typestack = ['ListItem'];
function match_ListItem ($stack = []) {
	$matchrule = "ListItem"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
	$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
	if ($subres !== \false) {
		$this->store($result, $subres, "skip");
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ListDefinition: "list[" __ ( items:ListItem ( __ "," __ items:ListItem )* )? __ ( "," __ )? "]" */
protected $match_ListDefinition_typestack = ['ListDefinition'];
function match_ListDefinition ($stack = []) {
	$matchrule = "ListDefinition"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_119 = \null;
	do {
		if (($subres = $this->literal('list[')) !== \false) { $result["text"] .= $subres; }
		else { $_119 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_119 = \false; break; }
		$res_112 = $result;
		$pos_112 = $this->pos;
		$_111 = \null;
		do {
			$matcher = 'match_'.'ListItem'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "items");
			}
			else { $_111 = \false; break; }
			while (\true) {
				$res_110 = $result;
				$pos_110 = $this->pos;
				$_109 = \null;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_109 = \false; break; }
					if (\substr($this->string,$this->pos,1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_109 = \false; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) { $this->store($result, $subres); }
					else { $_109 = \false; break; }
					$matcher = 'match_'.'ListItem'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "items");
					}
					else { $_109 = \false; break; }
					$_109 = \true; break;
				}
				while(0);
				if( $_109 === \false) {
					$result = $res_110;
					$this->pos = $pos_110;
					unset( $res_110 );
					unset( $pos_110 );
					break;
				}
			}
			$_111 = \true; break;
		}
		while(0);
		if( $_111 === \false) {
			$result = $res_112;
			$this->pos = $pos_112;
			unset( $res_112 );
			unset( $pos_112 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_119 = \false; break; }
		$res_117 = $result;
		$pos_117 = $this->pos;
		$_116 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_116 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_116 = \false; break; }
			$_116 = \true; break;
		}
		while(0);
		if( $_116 === \false) {
			$result = $res_117;
			$this->pos = $pos_117;
			unset( $res_117 );
			unset( $pos_117 );
		}
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_119 = \false; break; }
		$_119 = \true; break;
	}
	while(0);
	if( $_119 === \true ) { return $this->finalise($result); }
	if( $_119 === \false) { return \false; }
}


/* Value: skip:RangeLiteral | skip:Literal | skip:ListDefinition | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = ['Value'];
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_136 = \null;
	do {
		$res_121 = $result;
		$pos_121 = $this->pos;
		$matcher = 'match_'.'RangeLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_136 = \true; break;
		}
		$result = $res_121;
		$this->pos = $pos_121;
		$_134 = \null;
		do {
			$res_123 = $result;
			$pos_123 = $this->pos;
			$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_134 = \true; break;
			}
			$result = $res_123;
			$this->pos = $pos_123;
			$_132 = \null;
			do {
				$res_125 = $result;
				$pos_125 = $this->pos;
				$matcher = 'match_'.'ListDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_132 = \true; break;
				}
				$result = $res_125;
				$this->pos = $pos_125;
				$_130 = \null;
				do {
					$res_127 = $result;
					$pos_127 = $this->pos;
					$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_130 = \true; break;
					}
					$result = $res_127;
					$this->pos = $pos_127;
					$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_130 = \true; break;
					}
					$result = $res_127;
					$this->pos = $pos_127;
					$_130 = \false; break;
				}
				while(0);
				if( $_130 === \true ) { $_132 = \true; break; }
				$result = $res_125;
				$this->pos = $pos_125;
				$_132 = \false; break;
			}
			while(0);
			if( $_132 === \true ) { $_134 = \true; break; }
			$result = $res_123;
			$this->pos = $pos_123;
			$_134 = \false; break;
		}
		while(0);
		if( $_134 === \true ) { $_136 = \true; break; }
		$result = $res_121;
		$this->pos = $pos_121;
		$_136 = \false; break;
	}
	while(0);
	if( $_136 === \true ) { return $this->finalise($result); }
	if( $_136 === \false) { return \false; }
}


/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = ['VariableVector'];
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_140 = \null;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_140 = \false; break; }
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else { $_140 = \false; break; }
		$_140 = \true; break;
	}
	while(0);
	if( $_140 === \true ) { return $this->finalise($result); }
	if( $_140 === \false) { return \false; }
}


/* Vector: ( "[" __ ( arrayKey:Expression | arrayKey:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = ['Vector'];
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_156 = \null;
	do {
		$_153 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_153 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_153 = \false; break; }
			$_149 = \null;
			do {
				$_147 = \null;
				do {
					$res_144 = $result;
					$pos_144 = $this->pos;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "arrayKey");
						$_147 = \true; break;
					}
					$result = $res_144;
					$this->pos = $pos_144;
					$matcher = 'match_'.'Nothing'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "arrayKey");
						$_147 = \true; break;
					}
					$result = $res_144;
					$this->pos = $pos_144;
					$_147 = \false; break;
				}
				while(0);
				if( $_147 === \false) { $_149 = \false; break; }
				$_149 = \true; break;
			}
			while(0);
			if( $_149 === \false) { $_153 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_153 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_153 = \false; break; }
			$_153 = \true; break;
		}
		while(0);
		if( $_153 === \false) { $_156 = \false; break; }
		$res_155 = $result;
		$pos_155 = $this->pos;
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else {
			$result = $res_155;
			$this->pos = $pos_155;
			unset( $res_155 );
			unset( $pos_155 );
		}
		$_156 = \true; break;
	}
	while(0);
	if( $_156 === \true ) { return $this->finalise($result); }
	if( $_156 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = ['Mutable'];
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_161 = \null;
	do {
		$res_158 = $result;
		$pos_158 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_161 = \true; break;
		}
		$result = $res_158;
		$this->pos = $pos_158;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_161 = \true; break;
		}
		$result = $res_158;
		$this->pos = $pos_158;
		$_161 = \false; break;
	}
	while(0);
	if( $_161 === \true ) { return $this->finalise($result); }
	if( $_161 === \false) { return \false; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = ['ObjectResolutionOperator'];
function match_ObjectResolutionOperator ($stack = []) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (\substr($this->string,$this->pos,1) === '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = ['AddOperator'];
function match_AddOperator ($stack = []) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_167 = \null;
	do {
		$res_164 = $result;
		$pos_164 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_167 = \true; break;
		}
		$result = $res_164;
		$this->pos = $pos_164;
		if (\substr($this->string,$this->pos,1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_167 = \true; break;
		}
		$result = $res_164;
		$this->pos = $pos_164;
		$_167 = \false; break;
	}
	while(0);
	if( $_167 === \true ) { return $this->finalise($result); }
	if( $_167 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = ['MultiplyOperator'];
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_172 = \null;
	do {
		$res_169 = $result;
		$pos_169 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_172 = \true; break;
		}
		$result = $res_169;
		$this->pos = $pos_169;
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_172 = \true; break;
		}
		$result = $res_169;
		$this->pos = $pos_169;
		$_172 = \false; break;
	}
	while(0);
	if( $_172 === \true ) { return $this->finalise($result); }
	if( $_172 === \false) { return \false; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = ['AssignmentOperator'];
function match_AssignmentOperator ($stack = []) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (\substr($this->string,$this->pos,1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = ['ComparisonOperator'];
function match_ComparisonOperator ($stack = []) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_194 = \null;
	do {
		$res_175 = $result;
		$pos_175 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_194 = \true; break;
		}
		$result = $res_175;
		$this->pos = $pos_175;
		$_192 = \null;
		do {
			$res_177 = $result;
			$pos_177 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_192 = \true; break;
			}
			$result = $res_177;
			$this->pos = $pos_177;
			$_190 = \null;
			do {
				$res_179 = $result;
				$pos_179 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_190 = \true; break;
				}
				$result = $res_179;
				$this->pos = $pos_179;
				$_188 = \null;
				do {
					$res_181 = $result;
					$pos_181 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_188 = \true; break;
					}
					$result = $res_181;
					$this->pos = $pos_181;
					$_186 = \null;
					do {
						$res_183 = $result;
						$pos_183 = $this->pos;
						if (\substr($this->string,$this->pos,1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_186 = \true; break;
						}
						$result = $res_183;
						$this->pos = $pos_183;
						if (\substr($this->string,$this->pos,1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_186 = \true; break;
						}
						$result = $res_183;
						$this->pos = $pos_183;
						$_186 = \false; break;
					}
					while(0);
					if( $_186 === \true ) { $_188 = \true; break; }
					$result = $res_181;
					$this->pos = $pos_181;
					$_188 = \false; break;
				}
				while(0);
				if( $_188 === \true ) { $_190 = \true; break; }
				$result = $res_179;
				$this->pos = $pos_179;
				$_190 = \false; break;
			}
			while(0);
			if( $_190 === \true ) { $_192 = \true; break; }
			$result = $res_177;
			$this->pos = $pos_177;
			$_192 = \false; break;
		}
		while(0);
		if( $_192 === \true ) { $_194 = \true; break; }
		$result = $res_175;
		$this->pos = $pos_175;
		$_194 = \false; break;
	}
	while(0);
	if( $_194 === \true ) { return $this->finalise($result); }
	if( $_194 === \false) { return \false; }
}


/* AndOperator: "and" */
protected $match_AndOperator_typestack = ['AndOperator'];
function match_AndOperator ($stack = []) {
	$matchrule = "AndOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('and')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* OrOperator: "or" */
protected $match_OrOperator_typestack = ['OrOperator'];
function match_OrOperator ($stack = []) {
	$matchrule = "OrOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('or')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NegationOperator: "!" */
protected $match_NegationOperator_typestack = ['NegationOperator'];
function match_NegationOperator ($stack = []) {
	$matchrule = "NegationOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (\substr($this->string,$this->pos,1) === '!') {
		$this->pos += 1;
		$result["text"] .= '!';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:LogicalOr */
protected $match_Expression_typestack = ['Expression'];
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_206 = \null;
	do {
		$res_199 = $result;
		$pos_199 = $this->pos;
		$matcher = 'match_'.'AnonymousFunction'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_206 = \true; break;
		}
		$result = $res_199;
		$this->pos = $pos_199;
		$_204 = \null;
		do {
			$res_201 = $result;
			$pos_201 = $this->pos;
			$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_204 = \true; break;
			}
			$result = $res_201;
			$this->pos = $pos_201;
			$matcher = 'match_'.'LogicalOr'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_204 = \true; break;
			}
			$result = $res_201;
			$this->pos = $pos_201;
			$_204 = \false; break;
		}
		while(0);
		if( $_204 === \true ) { $_206 = \true; break; }
		$result = $res_199;
		$this->pos = $pos_199;
		$_206 = \false; break;
	}
	while(0);
	if( $_206 === \true ) { return $this->finalise($result); }
	if( $_206 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_213 = \null;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_213 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_213 = \false; break; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_213 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_213 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_213 = \false; break; }
		$_213 = \true; break;
	}
	while(0);
	if( $_213 === \true ) { return $this->finalise($result); }
	if( $_213 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_222 = \null;
	do {
		$matcher = 'match_'.'LogicalAnd'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_222 = \false; break; }
		while (\true) {
			$res_221 = $result;
			$pos_221 = $this->pos;
			$_220 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_220 = \false; break; }
				$matcher = 'match_'.'OrOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_220 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_220 = \false; break; }
				$matcher = 'match_'.'LogicalAnd'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_220 = \false; break; }
				$_220 = \true; break;
			}
			while(0);
			if( $_220 === \false) {
				$result = $res_221;
				$this->pos = $pos_221;
				unset( $res_221 );
				unset( $pos_221 );
				break;
			}
		}
		$_222 = \true; break;
	}
	while(0);
	if( $_222 === \true ) { return $this->finalise($result); }
	if( $_222 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_231 = \null;
	do {
		$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
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
				$matcher = 'match_'.'AndOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_229 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_229 = \false; break; }
				$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_229 = \false; break; }
				$_229 = \true; break;
			}
			while(0);
			if( $_229 === \false) {
				$result = $res_230;
				$this->pos = $pos_230;
				unset( $res_230 );
				unset( $pos_230 );
				break;
			}
		}
		$_231 = \true; break;
	}
	while(0);
	if( $_231 === \true ) { return $this->finalise($result); }
	if( $_231 === \false) { return \false; }
}


/* Comparison: operands:Addition ( __ ops:ComparisonOperator __ operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_240 = \null;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_240 = \false; break; }
		while (\true) {
			$res_239 = $result;
			$pos_239 = $this->pos;
			$_238 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_238 = \false; break; }
				$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_238 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_238 = \false; break; }
				$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_238 = \false; break; }
				$_238 = \true; break;
			}
			while(0);
			if( $_238 === \false) {
				$result = $res_239;
				$this->pos = $pos_239;
				unset( $res_239 );
				unset( $pos_239 );
				break;
			}
		}
		$_240 = \true; break;
	}
	while(0);
	if( $_240 === \true ) { return $this->finalise($result); }
	if( $_240 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_249 = \null;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_249 = \false; break; }
		while (\true) {
			$res_248 = $result;
			$pos_248 = $this->pos;
			$_247 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_247 = \false; break; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_247 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_247 = \false; break; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_247 = \false; break; }
				$_247 = \true; break;
			}
			while(0);
			if( $_247 === \false) {
				$result = $res_248;
				$this->pos = $pos_248;
				unset( $res_248 );
				unset( $pos_248 );
				break;
			}
		}
		$_249 = \true; break;
	}
	while(0);
	if( $_249 === \true ) { return $this->finalise($result); }
	if( $_249 === \false) { return \false; }
}


/* Multiplication: operands:Negation ( __ ops:MultiplyOperator __ operands:Negation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_258 = \null;
	do {
		$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_258 = \false; break; }
		while (\true) {
			$res_257 = $result;
			$pos_257 = $this->pos;
			$_256 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_256 = \false; break; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_256 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_256 = \false; break; }
				$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_256 = \false; break; }
				$_256 = \true; break;
			}
			while(0);
			if( $_256 === \false) {
				$result = $res_257;
				$this->pos = $pos_257;
				unset( $res_257 );
				unset( $pos_257 );
				break;
			}
		}
		$_258 = \true; break;
	}
	while(0);
	if( $_258 === \true ) { return $this->finalise($result); }
	if( $_258 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_264 = \null;
	do {
		while (\true) {
			$res_262 = $result;
			$pos_262 = $this->pos;
			$_261 = \null;
			do {
				$matcher = 'match_'.'NegationOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_261 = \false; break; }
				$_261 = \true; break;
			}
			while(0);
			if( $_261 === \false) {
				$result = $res_262;
				$this->pos = $pos_262;
				unset( $res_262 );
				unset( $pos_262 );
				break;
			}
		}
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_264 = \false; break; }
		$_264 = \true; break;
	}
	while(0);
	if( $_264 === \true ) { return $this->finalise($result); }
	if( $_264 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_284 = \null;
	do {
		$res_266 = $result;
		$pos_266 = $this->pos;
		$_281 = \null;
		do {
			$_278 = \null;
			do {
				$_276 = \null;
				do {
					$res_267 = $result;
					$pos_267 = $this->pos;
					$_273 = \null;
					do {
						if (\substr($this->string,$this->pos,1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_273 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_273 = \false; break; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_273 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_273 = \false; break; }
						if (\substr($this->string,$this->pos,1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_273 = \false; break; }
						$_273 = \true; break;
					}
					while(0);
					if( $_273 === \true ) { $_276 = \true; break; }
					$result = $res_267;
					$this->pos = $pos_267;
					$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_276 = \true; break;
					}
					$result = $res_267;
					$this->pos = $pos_267;
					$_276 = \false; break;
				}
				while(0);
				if( $_276 === \false) { $_278 = \false; break; }
				$_278 = \true; break;
			}
			while(0);
			if( $_278 === \false) { $_281 = \false; break; }
			$res_280 = $result;
			$pos_280 = $this->pos;
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_280;
				$this->pos = $pos_280;
				unset( $res_280 );
				unset( $pos_280 );
			}
			$_281 = \true; break;
		}
		while(0);
		if( $_281 === \true ) { $_284 = \true; break; }
		$result = $res_266;
		$this->pos = $pos_266;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_284 = \true; break;
		}
		$result = $res_266;
		$this->pos = $pos_266;
		$_284 = \false; break;
	}
	while(0);
	if( $_284 === \true ) { return $this->finalise($result); }
	if( $_284 === \false) { return \false; }
}


/* Chain: ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_298 = \null;
	do {
		$_295 = \null;
		do {
			$_293 = \null;
			do {
				$res_286 = $result;
				$pos_286 = $this->pos;
				$matcher = 'match_'.'Dereference'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_293 = \true; break;
				}
				$result = $res_286;
				$this->pos = $pos_286;
				$_291 = \null;
				do {
					$res_288 = $result;
					$pos_288 = $this->pos;
					$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_291 = \true; break;
					}
					$result = $res_288;
					$this->pos = $pos_288;
					$matcher = 'match_'.'ChainedFunction'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_291 = \true; break;
					}
					$result = $res_288;
					$this->pos = $pos_288;
					$_291 = \false; break;
				}
				while(0);
				if( $_291 === \true ) { $_293 = \true; break; }
				$result = $res_286;
				$this->pos = $pos_286;
				$_293 = \false; break;
			}
			while(0);
			if( $_293 === \false) { $_295 = \false; break; }
			$_295 = \true; break;
		}
		while(0);
		if( $_295 === \false) { $_298 = \false; break; }
		$res_297 = $result;
		$pos_297 = $this->pos;
		$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_297;
			$this->pos = $pos_297;
			unset( $res_297 );
			unset( $pos_297 );
		}
		$_298 = \true; break;
	}
	while(0);
	if( $_298 === \true ) { return $this->finalise($result); }
	if( $_298 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_305 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_305 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_305 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_305 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_305 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_305 = \false; break; }
		$_305 = \true; break;
	}
	while(0);
	if( $_305 === \true ) { return $this->finalise($result); }
	if( $_305 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_312 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_312 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_312 = \false; break; }
		$res_309 = $result;
		$pos_309 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_309;
			$this->pos = $pos_309;
			unset( $res_309 );
			unset( $pos_309 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_312 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_312 = \false; break; }
		$_312 = \true; break;
	}
	while(0);
	if( $_312 === \true ) { return $this->finalise($result); }
	if( $_312 === \false) { return \false; }
}


/* ChainedFunction: ObjectResolutionOperator fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_317 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_317 = \false; break; }
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_317 = \false; break; }
		$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_317 = \false; break; }
		$_317 = \true; break;
	}
	while(0);
	if( $_317 === \true ) { return $this->finalise($result); }
	if( $_317 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_326 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_326 = \false; break; }
		while (\true) {
			$res_325 = $result;
			$pos_325 = $this->pos;
			$_324 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_324 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_324 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_324 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_324 = \false; break; }
				$_324 = \true; break;
			}
			while(0);
			if( $_324 === \false) {
				$result = $res_325;
				$this->pos = $pos_325;
				unset( $res_325 );
				unset( $pos_325 );
				break;
			}
		}
		$_326 = \true; break;
	}
	while(0);
	if( $_326 === \true ) { return $this->finalise($result); }
	if( $_326 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_335 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_335 = \false; break; }
		while (\true) {
			$res_334 = $result;
			$pos_334 = $this->pos;
			$_333 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_333 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_333 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_333 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_333 = \false; break; }
				$_333 = \true; break;
			}
			while(0);
			if( $_333 === \false) {
				$result = $res_334;
				$this->pos = $pos_334;
				unset( $res_334 );
				unset( $pos_334 );
				break;
			}
		}
		$_335 = \true; break;
	}
	while(0);
	if( $_335 === \true ) { return $this->finalise($result); }
	if( $_335 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_348 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_348 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_348 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_348 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_348 = \false; break; }
		$res_343 = $result;
		$pos_343 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_343;
			$this->pos = $pos_343;
			unset( $res_343 );
			unset( $pos_343 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_348 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_348 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_348 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_348 = \false; break; }
		$_348 = \true; break;
	}
	while(0);
	if( $_348 === \true ) { return $this->finalise($result); }
	if( $_348 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_369 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_369 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_369 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_369 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_369 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_369 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_369 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_369 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_369 = \false; break; }
		$_359 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_359 = \false; break; }
			$_359 = \true; break;
		}
		while(0);
		if( $_359 === \false) { $_369 = \false; break; }
		$res_368 = $result;
		$pos_368 = $this->pos;
		$_367 = \null;
		do {
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_367 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_367 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_367 = \false; break; }
			$_365 = \null;
			do {
				$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_365 = \false; break; }
				$_365 = \true; break;
			}
			while(0);
			if( $_365 === \false) { $_367 = \false; break; }
			$_367 = \true; break;
		}
		while(0);
		if( $_367 === \false) {
			$result = $res_368;
			$this->pos = $pos_368;
			unset( $res_368 );
			unset( $pos_368 );
		}
		$_369 = \true; break;
	}
	while(0);
	if( $_369 === \true ) { return $this->finalise($result); }
	if( $_369 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_386 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_386 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_386 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_386 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_386 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_386 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_386 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_386 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_386 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_386 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_386 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_386 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_386 = \false; break; }
		$_384 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_384 = \false; break; }
			$_384 = \true; break;
		}
		while(0);
		if( $_384 === \false) { $_386 = \false; break; }
		$_386 = \true; break;
	}
	while(0);
	if( $_386 === \true ) { return $this->finalise($result); }
	if( $_386 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_399 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_399 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_399 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_399 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_399 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_399 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_399 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_399 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_399 = \false; break; }
		$_397 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_397 = \false; break; }
			$_397 = \true; break;
		}
		while(0);
		if( $_397 === \false) { $_399 = \false; break; }
		$_399 = \true; break;
	}
	while(0);
	if( $_399 === \true ) { return $this->finalise($result); }
	if( $_399 === \false) { return \false; }
}


/* CommandStatements: skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_408 = \null;
	do {
		$res_401 = $result;
		$pos_401 = $this->pos;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_408 = \true; break;
		}
		$result = $res_401;
		$this->pos = $pos_401;
		$_406 = \null;
		do {
			$res_403 = $result;
			$pos_403 = $this->pos;
			$matcher = 'match_'.'BreakStatement'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_406 = \true; break;
			}
			$result = $res_403;
			$this->pos = $pos_403;
			$matcher = 'match_'.'ContinueStatement'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_406 = \true; break;
			}
			$result = $res_403;
			$this->pos = $pos_403;
			$_406 = \false; break;
		}
		while(0);
		if( $_406 === \true ) { $_408 = \true; break; }
		$result = $res_401;
		$this->pos = $pos_401;
		$_408 = \false; break;
	}
	while(0);
	if( $_408 === \true ) { return $this->finalise($result); }
	if( $_408 === \false) { return \false; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_415 = \null;
	do {
		if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
		else { $_415 = \false; break; }
		$res_414 = $result;
		$pos_414 = $this->pos;
		$_413 = \null;
		do {
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_413 = \false; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "subject");
			}
			else { $_413 = \false; break; }
			$_413 = \true; break;
		}
		while(0);
		if( $_413 === \false) {
			$result = $res_414;
			$this->pos = $pos_414;
			unset( $res_414 );
			unset( $pos_414 );
		}
		$_415 = \true; break;
	}
	while(0);
	if( $_415 === \true ) { return $this->finalise($result); }
	if( $_415 === \false) { return \false; }
}


/* BreakStatement: "break" */
protected $match_BreakStatement_typestack = ['BreakStatement'];
function match_BreakStatement ($stack = []) {
	$matchrule = "BreakStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('break')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ContinueStatement: "continue" */
protected $match_ContinueStatement_typestack = ['ContinueStatement'];
function match_ContinueStatement ($stack = []) {
	$matchrule = "ContinueStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->literal('continue')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BlockStatements: &/[A-Za-z]/ ( skip:IfStatement | skip:WhileStatement | skip:ForStatement | skip:FunctionDefinition ) */
protected $match_BlockStatements_typestack = ['BlockStatements'];
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_435 = \null;
	do {
		$res_419 = $result;
		$pos_419 = $this->pos;
		if (($subres = $this->rx('/[A-Za-z]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_419;
			$this->pos = $pos_419;
		}
		else {
			$result = $res_419;
			$this->pos = $pos_419;
			$_435 = \false; break;
		}
		$_433 = \null;
		do {
			$_431 = \null;
			do {
				$res_420 = $result;
				$pos_420 = $this->pos;
				$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_431 = \true; break;
				}
				$result = $res_420;
				$this->pos = $pos_420;
				$_429 = \null;
				do {
					$res_422 = $result;
					$pos_422 = $this->pos;
					$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_429 = \true; break;
					}
					$result = $res_422;
					$this->pos = $pos_422;
					$_427 = \null;
					do {
						$res_424 = $result;
						$pos_424 = $this->pos;
						$matcher = 'match_'.'ForStatement'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_427 = \true; break;
						}
						$result = $res_424;
						$this->pos = $pos_424;
						$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_427 = \true; break;
						}
						$result = $res_424;
						$this->pos = $pos_424;
						$_427 = \false; break;
					}
					while(0);
					if( $_427 === \true ) { $_429 = \true; break; }
					$result = $res_422;
					$this->pos = $pos_422;
					$_429 = \false; break;
				}
				while(0);
				if( $_429 === \true ) { $_431 = \true; break; }
				$result = $res_420;
				$this->pos = $pos_420;
				$_431 = \false; break;
			}
			while(0);
			if( $_431 === \false) { $_433 = \false; break; }
			$_433 = \true; break;
		}
		while(0);
		if( $_433 === \false) { $_435 = \false; break; }
		$_435 = \true; break;
	}
	while(0);
	if( $_435 === \true ) { return $this->finalise($result); }
	if( $_435 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_449 = \null;
	do {
		$res_437 = $result;
		$pos_437 = $this->pos;
		if (($subres = $this->rx('/[\s\{\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_437;
			$this->pos = $pos_437;
			$_449 = \false; break;
		}
		else {
			$result = $res_437;
			$this->pos = $pos_437;
		}
		$_447 = \null;
		do {
			$_445 = \null;
			do {
				$res_438 = $result;
				$pos_438 = $this->pos;
				$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_445 = \true; break;
				}
				$result = $res_438;
				$this->pos = $pos_438;
				$_443 = \null;
				do {
					$res_440 = $result;
					$pos_440 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_443 = \true; break;
					}
					$result = $res_440;
					$this->pos = $pos_440;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_443 = \true; break;
					}
					$result = $res_440;
					$this->pos = $pos_440;
					$_443 = \false; break;
				}
				while(0);
				if( $_443 === \true ) { $_445 = \true; break; }
				$result = $res_438;
				$this->pos = $pos_438;
				$_445 = \false; break;
			}
			while(0);
			if( $_445 === \false) { $_447 = \false; break; }
			$_447 = \true; break;
		}
		while(0);
		if( $_447 === \false) { $_449 = \false; break; }
		$_449 = \true; break;
	}
	while(0);
	if( $_449 === \true ) { return $this->finalise($result); }
	if( $_449 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_457 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_457 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_457 = \false; break; }
		$res_455 = $result;
		$pos_455 = $this->pos;
		$_454 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_454 = \false; break; }
			$_454 = \true; break;
		}
		while(0);
		if( $_454 === \false) {
			$result = $res_455;
			$this->pos = $pos_455;
			unset( $res_455 );
			unset( $pos_455 );
		}
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_457 = \false; break; }
		$_457 = \true; break;
	}
	while(0);
	if( $_457 === \true ) { return $this->finalise($result); }
	if( $_457 === \false) { return \false; }
}


/* __: / [\s\n]* / */
protected $match____typestack = ['__'];
function match___ ($stack = []) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ [\s\n]* /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = ['NL'];
function match_NL ($stack = []) {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/ (?:\/\/[^\n]*)?\n /')) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* SEP: ";" | NL */
protected $match_SEP_typestack = ['SEP'];
function match_SEP ($stack = []) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_464 = \null;
	do {
		$res_461 = $result;
		$pos_461 = $this->pos;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_464 = \true; break;
		}
		$result = $res_461;
		$this->pos = $pos_461;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_464 = \true; break;
		}
		$result = $res_461;
		$this->pos = $pos_461;
		$_464 = \false; break;
	}
	while(0);
	if( $_464 === \true ) { return $this->finalise($result); }
	if( $_464 === \false) { return \false; }
}


/* Program: ( !/$/ __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_474 = \null;
	do {
		while (\true) {
			$res_472 = $result;
			$pos_472 = $this->pos;
			$_471 = \null;
			do {
				$res_466 = $result;
				$pos_466 = $this->pos;
				if (($subres = $this->rx('/$/')) !== \false) {
					$result["text"] .= $subres;
					$result = $res_466;
					$this->pos = $pos_466;
					$_471 = \false; break;
				}
				else {
					$result = $res_466;
					$this->pos = $pos_466;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_471 = \false; break; }
				$res_468 = $result;
				$pos_468 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_468;
					$this->pos = $pos_468;
					unset( $res_468 );
					unset( $pos_468 );
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_471 = \false; break; }
				$_471 = \true; break;
			}
			while(0);
			if( $_471 === \false) {
				$result = $res_472;
				$this->pos = $pos_472;
				unset( $res_472 );
				unset( $pos_472 );
				break;
			}
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_474 = \false; break; }
		$_474 = \true; break;
	}
	while(0);
	if( $_474 === \true ) { return $this->finalise($result); }
	if( $_474 === \false) { return \false; }
}




}
