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


/* NumberLiteral: /-?\d[\d_]*(\.[\d_]+)?/ */
protected $match_NumberLiteral_typestack = ['NumberLiteral'];
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	if (($subres = $this->rx('/-?\d[\d_]*(\.[\d_]+)?/')) !== \false) {
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


/* Value: skip:RangeLiteral | skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = ['Value'];
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_112 = \null;
	do {
		$res_101 = $result;
		$pos_101 = $this->pos;
		$matcher = 'match_'.'RangeLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_112 = \true; break;
		}
		$result = $res_101;
		$this->pos = $pos_101;
		$_110 = \null;
		do {
			$res_103 = $result;
			$pos_103 = $this->pos;
			$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_110 = \true; break;
			}
			$result = $res_103;
			$this->pos = $pos_103;
			$_108 = \null;
			do {
				$res_105 = $result;
				$pos_105 = $this->pos;
				$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_108 = \true; break;
				}
				$result = $res_105;
				$this->pos = $pos_105;
				$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_108 = \true; break;
				}
				$result = $res_105;
				$this->pos = $pos_105;
				$_108 = \false; break;
			}
			while(0);
			if( $_108 === \true ) { $_110 = \true; break; }
			$result = $res_103;
			$this->pos = $pos_103;
			$_110 = \false; break;
		}
		while(0);
		if( $_110 === \true ) { $_112 = \true; break; }
		$result = $res_101;
		$this->pos = $pos_101;
		$_112 = \false; break;
	}
	while(0);
	if( $_112 === \true ) { return $this->finalise($result); }
	if( $_112 === \false) { return \false; }
}


/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = ['VariableVector'];
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_116 = \null;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_116 = \false; break; }
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else { $_116 = \false; break; }
		$_116 = \true; break;
	}
	while(0);
	if( $_116 === \true ) { return $this->finalise($result); }
	if( $_116 === \false) { return \false; }
}


/* Vector: ( "[" __ ( arrayKey:Expression | arrayKey:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = ['Vector'];
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_132 = \null;
	do {
		$_129 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_129 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_129 = \false; break; }
			$_125 = \null;
			do {
				$_123 = \null;
				do {
					$res_120 = $result;
					$pos_120 = $this->pos;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "arrayKey");
						$_123 = \true; break;
					}
					$result = $res_120;
					$this->pos = $pos_120;
					$matcher = 'match_'.'Nothing'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "arrayKey");
						$_123 = \true; break;
					}
					$result = $res_120;
					$this->pos = $pos_120;
					$_123 = \false; break;
				}
				while(0);
				if( $_123 === \false) { $_125 = \false; break; }
				$_125 = \true; break;
			}
			while(0);
			if( $_125 === \false) { $_129 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_129 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_129 = \false; break; }
			$_129 = \true; break;
		}
		while(0);
		if( $_129 === \false) { $_132 = \false; break; }
		$res_131 = $result;
		$pos_131 = $this->pos;
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "vector");
		}
		else {
			$result = $res_131;
			$this->pos = $pos_131;
			unset( $res_131 );
			unset( $pos_131 );
		}
		$_132 = \true; break;
	}
	while(0);
	if( $_132 === \true ) { return $this->finalise($result); }
	if( $_132 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = ['Mutable'];
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_137 = \null;
	do {
		$res_134 = $result;
		$pos_134 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_137 = \true; break;
		}
		$result = $res_134;
		$this->pos = $pos_134;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_137 = \true; break;
		}
		$result = $res_134;
		$this->pos = $pos_134;
		$_137 = \false; break;
	}
	while(0);
	if( $_137 === \true ) { return $this->finalise($result); }
	if( $_137 === \false) { return \false; }
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
	$_143 = \null;
	do {
		$res_140 = $result;
		$pos_140 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_143 = \true; break;
		}
		$result = $res_140;
		$this->pos = $pos_140;
		if (\substr($this->string,$this->pos,1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_143 = \true; break;
		}
		$result = $res_140;
		$this->pos = $pos_140;
		$_143 = \false; break;
	}
	while(0);
	if( $_143 === \true ) { return $this->finalise($result); }
	if( $_143 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = ['MultiplyOperator'];
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_148 = \null;
	do {
		$res_145 = $result;
		$pos_145 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_148 = \true; break;
		}
		$result = $res_145;
		$this->pos = $pos_145;
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_148 = \true; break;
		}
		$result = $res_145;
		$this->pos = $pos_145;
		$_148 = \false; break;
	}
	while(0);
	if( $_148 === \true ) { return $this->finalise($result); }
	if( $_148 === \false) { return \false; }
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
	$_170 = \null;
	do {
		$res_151 = $result;
		$pos_151 = $this->pos;
		if (($subres = $this->literal('==')) !== \false) {
			$result["text"] .= $subres;
			$_170 = \true; break;
		}
		$result = $res_151;
		$this->pos = $pos_151;
		$_168 = \null;
		do {
			$res_153 = $result;
			$pos_153 = $this->pos;
			if (($subres = $this->literal('!=')) !== \false) {
				$result["text"] .= $subres;
				$_168 = \true; break;
			}
			$result = $res_153;
			$this->pos = $pos_153;
			$_166 = \null;
			do {
				$res_155 = $result;
				$pos_155 = $this->pos;
				if (($subres = $this->literal('>=')) !== \false) {
					$result["text"] .= $subres;
					$_166 = \true; break;
				}
				$result = $res_155;
				$this->pos = $pos_155;
				$_164 = \null;
				do {
					$res_157 = $result;
					$pos_157 = $this->pos;
					if (($subres = $this->literal('<=')) !== \false) {
						$result["text"] .= $subres;
						$_164 = \true; break;
					}
					$result = $res_157;
					$this->pos = $pos_157;
					$_162 = \null;
					do {
						$res_159 = $result;
						$pos_159 = $this->pos;
						if (\substr($this->string,$this->pos,1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_162 = \true; break;
						}
						$result = $res_159;
						$this->pos = $pos_159;
						if (\substr($this->string,$this->pos,1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_162 = \true; break;
						}
						$result = $res_159;
						$this->pos = $pos_159;
						$_162 = \false; break;
					}
					while(0);
					if( $_162 === \true ) { $_164 = \true; break; }
					$result = $res_157;
					$this->pos = $pos_157;
					$_164 = \false; break;
				}
				while(0);
				if( $_164 === \true ) { $_166 = \true; break; }
				$result = $res_155;
				$this->pos = $pos_155;
				$_166 = \false; break;
			}
			while(0);
			if( $_166 === \true ) { $_168 = \true; break; }
			$result = $res_153;
			$this->pos = $pos_153;
			$_168 = \false; break;
		}
		while(0);
		if( $_168 === \true ) { $_170 = \true; break; }
		$result = $res_151;
		$this->pos = $pos_151;
		$_170 = \false; break;
	}
	while(0);
	if( $_170 === \true ) { return $this->finalise($result); }
	if( $_170 === \false) { return \false; }
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
	$_182 = \null;
	do {
		$res_175 = $result;
		$pos_175 = $this->pos;
		$matcher = 'match_'.'AnonymousFunction'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_182 = \true; break;
		}
		$result = $res_175;
		$this->pos = $pos_175;
		$_180 = \null;
		do {
			$res_177 = $result;
			$pos_177 = $this->pos;
			$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_180 = \true; break;
			}
			$result = $res_177;
			$this->pos = $pos_177;
			$matcher = 'match_'.'LogicalOr'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_180 = \true; break;
			}
			$result = $res_177;
			$this->pos = $pos_177;
			$_180 = \false; break;
		}
		while(0);
		if( $_180 === \true ) { $_182 = \true; break; }
		$result = $res_175;
		$this->pos = $pos_175;
		$_182 = \false; break;
	}
	while(0);
	if( $_182 === \true ) { return $this->finalise($result); }
	if( $_182 === \false) { return \false; }
}


/* Assignment: left:Mutable __ AssignmentOperator __ right:Expression */
protected $match_Assignment_typestack = ['Assignment'];
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_189 = \null;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_189 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_189 = \false; break; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_189 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_189 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "right");
		}
		else { $_189 = \false; break; }
		$_189 = \true; break;
	}
	while(0);
	if( $_189 === \true ) { return $this->finalise($result); }
	if( $_189 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = ['LogicalOr'];
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_198 = \null;
	do {
		$matcher = 'match_'.'LogicalAnd'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_198 = \false; break; }
		while (\true) {
			$res_197 = $result;
			$pos_197 = $this->pos;
			$_196 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_196 = \false; break; }
				$matcher = 'match_'.'OrOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_196 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_196 = \false; break; }
				$matcher = 'match_'.'LogicalAnd'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_196 = \false; break; }
				$_196 = \true; break;
			}
			while(0);
			if( $_196 === \false) {
				$result = $res_197;
				$this->pos = $pos_197;
				unset( $res_197 );
				unset( $pos_197 );
				break;
			}
		}
		$_198 = \true; break;
	}
	while(0);
	if( $_198 === \true ) { return $this->finalise($result); }
	if( $_198 === \false) { return \false; }
}


/* LogicalAnd: operands:Comparison ( ] ops:AndOperator ] operands:Comparison )* */
protected $match_LogicalAnd_typestack = ['LogicalAnd'];
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_207 = \null;
	do {
		$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_207 = \false; break; }
		while (\true) {
			$res_206 = $result;
			$pos_206 = $this->pos;
			$_205 = \null;
			do {
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_205 = \false; break; }
				$matcher = 'match_'.'AndOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_205 = \false; break; }
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				else { $_205 = \false; break; }
				$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_205 = \false; break; }
				$_205 = \true; break;
			}
			while(0);
			if( $_205 === \false) {
				$result = $res_206;
				$this->pos = $pos_206;
				unset( $res_206 );
				unset( $pos_206 );
				break;
			}
		}
		$_207 = \true; break;
	}
	while(0);
	if( $_207 === \true ) { return $this->finalise($result); }
	if( $_207 === \false) { return \false; }
}


/* Comparison: operands:Addition ( __ ops:ComparisonOperator __ operands:Addition )* */
protected $match_Comparison_typestack = ['Comparison'];
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_216 = \null;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_216 = \false; break; }
		while (\true) {
			$res_215 = $result;
			$pos_215 = $this->pos;
			$_214 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_214 = \false; break; }
				$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_214 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_214 = \false; break; }
				$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_214 = \false; break; }
				$_214 = \true; break;
			}
			while(0);
			if( $_214 === \false) {
				$result = $res_215;
				$this->pos = $pos_215;
				unset( $res_215 );
				unset( $pos_215 );
				break;
			}
		}
		$_216 = \true; break;
	}
	while(0);
	if( $_216 === \true ) { return $this->finalise($result); }
	if( $_216 === \false) { return \false; }
}


/* Addition: operands:Multiplication ( __ ops:AddOperator __ operands:Multiplication )* */
protected $match_Addition_typestack = ['Addition'];
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_225 = \null;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_225 = \false; break; }
		while (\true) {
			$res_224 = $result;
			$pos_224 = $this->pos;
			$_223 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_223 = \false; break; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_223 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_223 = \false; break; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_223 = \false; break; }
				$_223 = \true; break;
			}
			while(0);
			if( $_223 === \false) {
				$result = $res_224;
				$this->pos = $pos_224;
				unset( $res_224 );
				unset( $pos_224 );
				break;
			}
		}
		$_225 = \true; break;
	}
	while(0);
	if( $_225 === \true ) { return $this->finalise($result); }
	if( $_225 === \false) { return \false; }
}


/* Multiplication: operands:Negation ( __ ops:MultiplyOperator __ operands:Negation )* */
protected $match_Multiplication_typestack = ['Multiplication'];
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_234 = \null;
	do {
		$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "operands");
		}
		else { $_234 = \false; break; }
		while (\true) {
			$res_233 = $result;
			$pos_233 = $this->pos;
			$_232 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_232 = \false; break; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "ops");
				}
				else { $_232 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_232 = \false; break; }
				$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "operands");
				}
				else { $_232 = \false; break; }
				$_232 = \true; break;
			}
			while(0);
			if( $_232 === \false) {
				$result = $res_233;
				$this->pos = $pos_233;
				unset( $res_233 );
				unset( $pos_233 );
				break;
			}
		}
		$_234 = \true; break;
	}
	while(0);
	if( $_234 === \true ) { return $this->finalise($result); }
	if( $_234 === \false) { return \false; }
}


/* Negation: ( nots:NegationOperator )* core:Operand */
protected $match_Negation_typestack = ['Negation'];
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_240 = \null;
	do {
		while (\true) {
			$res_238 = $result;
			$pos_238 = $this->pos;
			$_237 = \null;
			do {
				$matcher = 'match_'.'NegationOperator'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "nots");
				}
				else { $_237 = \false; break; }
				$_237 = \true; break;
			}
			while(0);
			if( $_237 === \false) {
				$result = $res_238;
				$this->pos = $pos_238;
				unset( $res_238 );
				unset( $pos_238 );
				break;
			}
		}
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "core");
		}
		else { $_240 = \false; break; }
		$_240 = \true; break;
	}
	while(0);
	if( $_240 === \true ) { return $this->finalise($result); }
	if( $_240 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = ['Operand'];
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_260 = \null;
	do {
		$res_242 = $result;
		$pos_242 = $this->pos;
		$_257 = \null;
		do {
			$_254 = \null;
			do {
				$_252 = \null;
				do {
					$res_243 = $result;
					$pos_243 = $this->pos;
					$_249 = \null;
					do {
						if (\substr($this->string,$this->pos,1) === '(') {
							$this->pos += 1;
							$result["text"] .= '(';
						}
						else { $_249 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_249 = \false; break; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "core");
						}
						else { $_249 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) { $this->store($result, $subres); }
						else { $_249 = \false; break; }
						if (\substr($this->string,$this->pos,1) === ')') {
							$this->pos += 1;
							$result["text"] .= ')';
						}
						else { $_249 = \false; break; }
						$_249 = \true; break;
					}
					while(0);
					if( $_249 === \true ) { $_252 = \true; break; }
					$result = $res_243;
					$this->pos = $pos_243;
					$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_252 = \true; break;
					}
					$result = $res_243;
					$this->pos = $pos_243;
					$_252 = \false; break;
				}
				while(0);
				if( $_252 === \false) { $_254 = \false; break; }
				$_254 = \true; break;
			}
			while(0);
			if( $_254 === \false) { $_257 = \false; break; }
			$res_256 = $result;
			$pos_256 = $this->pos;
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "chain");
			}
			else {
				$result = $res_256;
				$this->pos = $pos_256;
				unset( $res_256 );
				unset( $pos_256 );
			}
			$_257 = \true; break;
		}
		while(0);
		if( $_257 === \true ) { $_260 = \true; break; }
		$result = $res_242;
		$this->pos = $pos_242;
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_260 = \true; break;
		}
		$result = $res_242;
		$this->pos = $pos_242;
		$_260 = \false; break;
	}
	while(0);
	if( $_260 === \true ) { return $this->finalise($result); }
	if( $_260 === \false) { return \false; }
}


/* Chain: ( core:Dereference | core:Invocation | core:ChainedFunction ) chain:Chain? */
protected $match_Chain_typestack = ['Chain'];
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_274 = \null;
	do {
		$_271 = \null;
		do {
			$_269 = \null;
			do {
				$res_262 = $result;
				$pos_262 = $this->pos;
				$matcher = 'match_'.'Dereference'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "core");
					$_269 = \true; break;
				}
				$result = $res_262;
				$this->pos = $pos_262;
				$_267 = \null;
				do {
					$res_264 = $result;
					$pos_264 = $this->pos;
					$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_267 = \true; break;
					}
					$result = $res_264;
					$this->pos = $pos_264;
					$matcher = 'match_'.'ChainedFunction'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "core");
						$_267 = \true; break;
					}
					$result = $res_264;
					$this->pos = $pos_264;
					$_267 = \false; break;
				}
				while(0);
				if( $_267 === \true ) { $_269 = \true; break; }
				$result = $res_262;
				$this->pos = $pos_262;
				$_269 = \false; break;
			}
			while(0);
			if( $_269 === \false) { $_271 = \false; break; }
			$_271 = \true; break;
		}
		while(0);
		if( $_271 === \false) { $_274 = \false; break; }
		$res_273 = $result;
		$pos_273 = $this->pos;
		$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "chain");
		}
		else {
			$result = $res_273;
			$this->pos = $pos_273;
			unset( $res_273 );
			unset( $pos_273 );
		}
		$_274 = \true; break;
	}
	while(0);
	if( $_274 === \true ) { return $this->finalise($result); }
	if( $_274 === \false) { return \false; }
}


/* Dereference: "[" __ key:Expression __ "]" */
protected $match_Dereference_typestack = ['Dereference'];
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_281 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_281 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_281 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "key");
		}
		else { $_281 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_281 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_281 = \false; break; }
		$_281 = \true; break;
	}
	while(0);
	if( $_281 === \true ) { return $this->finalise($result); }
	if( $_281 === \false) { return \false; }
}


/* Invocation: "(" __ args:ArgumentList? __ ")" */
protected $match_Invocation_typestack = ['Invocation'];
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_288 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_288 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_288 = \false; break; }
		$res_285 = $result;
		$pos_285 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else {
			$result = $res_285;
			$this->pos = $pos_285;
			unset( $res_285 );
			unset( $pos_285 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_288 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_288 = \false; break; }
		$_288 = \true; break;
	}
	while(0);
	if( $_288 === \true ) { return $this->finalise($result); }
	if( $_288 === \false) { return \false; }
}


/* ChainedFunction: ObjectResolutionOperator fn:Variable invo:Invocation */
protected $match_ChainedFunction_typestack = ['ChainedFunction'];
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_293 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_293 = \false; break; }
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "fn");
		}
		else { $_293 = \false; break; }
		$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "invo");
		}
		else { $_293 = \false; break; }
		$_293 = \true; break;
	}
	while(0);
	if( $_293 === \true ) { return $this->finalise($result); }
	if( $_293 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = ['ArgumentList'];
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_302 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "args");
		}
		else { $_302 = \false; break; }
		while (\true) {
			$res_301 = $result;
			$pos_301 = $this->pos;
			$_300 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_300 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_300 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_300 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "args");
				}
				else { $_300 = \false; break; }
				$_300 = \true; break;
			}
			while(0);
			if( $_300 === \false) {
				$result = $res_301;
				$this->pos = $pos_301;
				unset( $res_301 );
				unset( $pos_301 );
				break;
			}
		}
		$_302 = \true; break;
	}
	while(0);
	if( $_302 === \true ) { return $this->finalise($result); }
	if( $_302 === \false) { return \false; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( __ "," __ skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = ['FunctionDefinitionArgumentList'];
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_311 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
		}
		else { $_311 = \false; break; }
		while (\true) {
			$res_310 = $result;
			$pos_310 = $this->pos;
			$_309 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_309 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_309 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_309 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
				}
				else { $_309 = \false; break; }
				$_309 = \true; break;
			}
			while(0);
			if( $_309 === \false) {
				$result = $res_310;
				$this->pos = $pos_310;
				unset( $res_310 );
				unset( $pos_310 );
				break;
			}
		}
		$_311 = \true; break;
	}
	while(0);
	if( $_311 === \true ) { return $this->finalise($result); }
	if( $_311 === \false) { return \false; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ params:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = ['FunctionDefinition'];
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_324 = \null;
	do {
		if (($subres = $this->literal('function')) !== \false) { $result["text"] .= $subres; }
		else { $_324 = \false; break; }
		if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
		else { $_324 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "function");
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_324 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_324 = \false; break; }
		$res_319 = $result;
		$pos_319 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "params");
		}
		else {
			$result = $res_319;
			$this->pos = $pos_319;
			unset( $res_319 );
			unset( $pos_319 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_324 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_324 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "body");
		}
		else { $_324 = \false; break; }
		$_324 = \true; break;
	}
	while(0);
	if( $_324 === \true ) { return $this->finalise($result); }
	if( $_324 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) ( __ "else" __ ( else:Block ) )? */
protected $match_IfStatement_typestack = ['IfStatement'];
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_345 = \null;
	do {
		if (($subres = $this->literal('if')) !== \false) { $result["text"] .= $subres; }
		else { $_345 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_345 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_345 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_345 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_345 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_345 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_345 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_345 = \false; break; }
		$_335 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_335 = \false; break; }
			$_335 = \true; break;
		}
		while(0);
		if( $_335 === \false) { $_345 = \false; break; }
		$res_344 = $result;
		$pos_344 = $this->pos;
		$_343 = \null;
		do {
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_343 = \false; break; }
			if (($subres = $this->literal('else')) !== \false) { $result["text"] .= $subres; }
			else { $_343 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) { $this->store($result, $subres); }
			else { $_343 = \false; break; }
			$_341 = \null;
			do {
				$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "else");
				}
				else { $_341 = \false; break; }
				$_341 = \true; break;
			}
			while(0);
			if( $_341 === \false) { $_343 = \false; break; }
			$_343 = \true; break;
		}
		while(0);
		if( $_343 === \false) {
			$result = $res_344;
			$this->pos = $pos_344;
			unset( $res_344 );
			unset( $pos_344 );
		}
		$_345 = \true; break;
	}
	while(0);
	if( $_345 === \true ) { return $this->finalise($result); }
	if( $_345 === \false) { return \false; }
}


/* ForStatement: "for" __ "(" __ item:VariableName __ "in" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_ForStatement_typestack = ['ForStatement'];
function match_ForStatement ($stack = []) {
	$matchrule = "ForStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_362 = \null;
	do {
		if (($subres = $this->literal('for')) !== \false) { $result["text"] .= $subres; }
		else { $_362 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_362 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "item");
		}
		else { $_362 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		if (($subres = $this->literal('in')) !== \false) { $result["text"] .= $subres; }
		else { $_362 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_362 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_362 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_362 = \false; break; }
		$_360 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_360 = \false; break; }
			$_360 = \true; break;
		}
		while(0);
		if( $_360 === \false) { $_362 = \false; break; }
		$_362 = \true; break;
	}
	while(0);
	if( $_362 === \true ) { return $this->finalise($result); }
	if( $_362 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = ['WhileStatement'];
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_375 = \null;
	do {
		if (($subres = $this->literal('while')) !== \false) { $result["text"] .= $subres; }
		else { $_375 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_375 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_375 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_375 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "left");
		}
		else { $_375 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_375 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_375 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_375 = \false; break; }
		$_373 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "right");
			}
			else { $_373 = \false; break; }
			$_373 = \true; break;
		}
		while(0);
		if( $_373 === \false) { $_375 = \false; break; }
		$_375 = \true; break;
	}
	while(0);
	if( $_375 === \true ) { return $this->finalise($result); }
	if( $_375 === \false) { return \false; }
}


/* CommandStatements: skip:ReturnStatement | skip:BreakStatement | skip:ContinueStatement */
protected $match_CommandStatements_typestack = ['CommandStatements'];
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_384 = \null;
	do {
		$res_377 = $result;
		$pos_377 = $this->pos;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres, "skip");
			$_384 = \true; break;
		}
		$result = $res_377;
		$this->pos = $pos_377;
		$_382 = \null;
		do {
			$res_379 = $result;
			$pos_379 = $this->pos;
			$matcher = 'match_'.'BreakStatement'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_382 = \true; break;
			}
			$result = $res_379;
			$this->pos = $pos_379;
			$matcher = 'match_'.'ContinueStatement'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
				$_382 = \true; break;
			}
			$result = $res_379;
			$this->pos = $pos_379;
			$_382 = \false; break;
		}
		while(0);
		if( $_382 === \true ) { $_384 = \true; break; }
		$result = $res_377;
		$this->pos = $pos_377;
		$_384 = \false; break;
	}
	while(0);
	if( $_384 === \true ) { return $this->finalise($result); }
	if( $_384 === \false) { return \false; }
}


/* ReturnStatement: ( "return" [ ( subject:Expression )? ) | ( "return" &SEP ) */
protected $match_ReturnStatement_typestack = ['ReturnStatement'];
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_398 = \null;
	do {
		$res_386 = $result;
		$pos_386 = $this->pos;
		$_392 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_392 = \false; break; }
			if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
			else { $_392 = \false; break; }
			$res_391 = $result;
			$pos_391 = $this->pos;
			$_390 = \null;
			do {
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "subject");
				}
				else { $_390 = \false; break; }
				$_390 = \true; break;
			}
			while(0);
			if( $_390 === \false) {
				$result = $res_391;
				$this->pos = $pos_391;
				unset( $res_391 );
				unset( $pos_391 );
			}
			$_392 = \true; break;
		}
		while(0);
		if( $_392 === \true ) { $_398 = \true; break; }
		$result = $res_386;
		$this->pos = $pos_386;
		$_396 = \null;
		do {
			if (($subres = $this->literal('return')) !== \false) { $result["text"] .= $subres; }
			else { $_396 = \false; break; }
			$res_395 = $result;
			$pos_395 = $this->pos;
			$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres);
				$result = $res_395;
				$this->pos = $pos_395;
			}
			else {
				$result = $res_395;
				$this->pos = $pos_395;
				$_396 = \false; break;
			}
			$_396 = \true; break;
		}
		while(0);
		if( $_396 === \true ) { $_398 = \true; break; }
		$result = $res_386;
		$this->pos = $pos_386;
		$_398 = \false; break;
	}
	while(0);
	if( $_398 === \true ) { return $this->finalise($result); }
	if( $_398 === \false) { return \false; }
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
	$_418 = \null;
	do {
		$res_402 = $result;
		$pos_402 = $this->pos;
		if (($subres = $this->rx('/[A-Za-z]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_402;
			$this->pos = $pos_402;
		}
		else {
			$result = $res_402;
			$this->pos = $pos_402;
			$_418 = \false; break;
		}
		$_416 = \null;
		do {
			$_414 = \null;
			do {
				$res_403 = $result;
				$pos_403 = $this->pos;
				$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_414 = \true; break;
				}
				$result = $res_403;
				$this->pos = $pos_403;
				$_412 = \null;
				do {
					$res_405 = $result;
					$pos_405 = $this->pos;
					$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
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
						$matcher = 'match_'.'ForStatement'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_410 = \true; break;
						}
						$result = $res_407;
						$this->pos = $pos_407;
						$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
						$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
						if ($subres !== \false) {
							$this->store($result, $subres, "skip");
							$_410 = \true; break;
						}
						$result = $res_407;
						$this->pos = $pos_407;
						$_410 = \false; break;
					}
					while(0);
					if( $_410 === \true ) { $_412 = \true; break; }
					$result = $res_405;
					$this->pos = $pos_405;
					$_412 = \false; break;
				}
				while(0);
				if( $_412 === \true ) { $_414 = \true; break; }
				$result = $res_403;
				$this->pos = $pos_403;
				$_414 = \false; break;
			}
			while(0);
			if( $_414 === \false) { $_416 = \false; break; }
			$_416 = \true; break;
		}
		while(0);
		if( $_416 === \false) { $_418 = \false; break; }
		$_418 = \true; break;
	}
	while(0);
	if( $_418 === \true ) { return $this->finalise($result); }
	if( $_418 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = ['Statement'];
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_432 = \null;
	do {
		$res_420 = $result;
		$pos_420 = $this->pos;
		if (($subres = $this->rx('/[\s\{\};]/')) !== \false) {
			$result["text"] .= $subres;
			$result = $res_420;
			$this->pos = $pos_420;
			$_432 = \false; break;
		}
		else {
			$result = $res_420;
			$this->pos = $pos_420;
		}
		$_430 = \null;
		do {
			$_428 = \null;
			do {
				$res_421 = $result;
				$pos_421 = $this->pos;
				$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "skip");
					$_428 = \true; break;
				}
				$result = $res_421;
				$this->pos = $pos_421;
				$_426 = \null;
				do {
					$res_423 = $result;
					$pos_423 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_426 = \true; break;
					}
					$result = $res_423;
					$this->pos = $pos_423;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
					if ($subres !== \false) {
						$this->store($result, $subres, "skip");
						$_426 = \true; break;
					}
					$result = $res_423;
					$this->pos = $pos_423;
					$_426 = \false; break;
				}
				while(0);
				if( $_426 === \true ) { $_428 = \true; break; }
				$result = $res_421;
				$this->pos = $pos_421;
				$_428 = \false; break;
			}
			while(0);
			if( $_428 === \false) { $_430 = \false; break; }
			$_430 = \true; break;
		}
		while(0);
		if( $_430 === \false) { $_432 = \false; break; }
		$_432 = \true; break;
	}
	while(0);
	if( $_432 === \true ) { return $this->finalise($result); }
	if( $_432 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = ['Block'];
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_440 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_440 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_440 = \false; break; }
		$res_438 = $result;
		$pos_438 = $this->pos;
		$_437 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
			if ($subres !== \false) {
				$this->store($result, $subres, "skip");
			}
			else { $_437 = \false; break; }
			$_437 = \true; break;
		}
		while(0);
		if( $_437 === \false) {
			$result = $res_438;
			$this->pos = $pos_438;
			unset( $res_438 );
			unset( $pos_438 );
		}
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_440 = \false; break; }
		$_440 = \true; break;
	}
	while(0);
	if( $_440 === \true ) { return $this->finalise($result); }
	if( $_440 === \false) { return \false; }
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
	$_447 = \null;
	do {
		$res_444 = $result;
		$pos_444 = $this->pos;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_447 = \true; break;
		}
		$result = $res_444;
		$this->pos = $pos_444;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) {
			$this->store($result, $subres);
			$_447 = \true; break;
		}
		$result = $res_444;
		$this->pos = $pos_444;
		$_447 = \false; break;
	}
	while(0);
	if( $_447 === \true ) { return $this->finalise($result); }
	if( $_447 === \false) { return \false; }
}


/* Program: ( !/$/ __ stmts:Statement? > SEP )* __ */
protected $match_Program_typestack = ['Program'];
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null); $newStack = \array_merge($stack, [$result]);
	$_457 = \null;
	do {
		while (\true) {
			$res_455 = $result;
			$pos_455 = $this->pos;
			$_454 = \null;
			do {
				$res_449 = $result;
				$pos_449 = $this->pos;
				if (($subres = $this->rx('/$/')) !== \false) {
					$result["text"] .= $subres;
					$result = $res_449;
					$this->pos = $pos_449;
					$_454 = \false; break;
				}
				else {
					$result = $res_449;
					$this->pos = $pos_449;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_454 = \false; break; }
				$res_451 = $result;
				$pos_451 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) {
					$this->store($result, $subres, "stmts");
				}
				else {
					$result = $res_451;
					$this->pos = $pos_451;
					unset( $res_451 );
					unset( $pos_451 );
				}
				if (($subres = $this->whitespace()) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
				if ($subres !== \false) { $this->store($result, $subres); }
				else { $_454 = \false; break; }
				$_454 = \true; break;
			}
			while(0);
			if( $_454 === \false) {
				$result = $res_455;
				$this->pos = $pos_455;
				unset( $res_455 );
				unset( $pos_455 );
				break;
			}
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = $this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher($newStack));
		if ($subres !== \false) { $this->store($result, $subres); }
		else { $_457 = \false; break; }
		$_457 = \true; break;
	}
	while(0);
	if( $_457 === \true ) { return $this->finalise($result); }
	if( $_457 === \false) { return \false; }
}




}
