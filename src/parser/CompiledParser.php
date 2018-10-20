<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

	// Add these properties so PHPStan doesn't complain about undefined properties.

	/** @var int **/
	public $pos;

	/** @var string **/
	public $string;

/* StringLiteral: / (?:".*?(?<!\\)")|(?:'.*?(?<!\\)') /s */
protected $match_StringLiteral_typestack = array('StringLiteral');
function match_StringLiteral ($stack = []) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ (?:".*?(?<!\\\\)")|(?:\'.*?(?<!\\\\)\') /s' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = array('NumberLiteral');
function match_NumberLiteral ($stack = []) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/-?\d+(\.\d+)?/' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = array('BoolLiteral');
function match_BoolLiteral ($stack = []) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	$_5 = \null;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== \false) {
			$result["text"] .= $subres;
			$_5 = \true; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		if (( $subres = $this->literal( 'false' ) ) !== \false) {
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
protected $match_NullLiteral_typestack = array('NullLiteral');
function match_NullLiteral ($stack = []) {
	$matchrule = "NullLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->literal( 'null' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* RegexLiteral: "r" core:StringLiteral */
protected $match_RegexLiteral_typestack = array('RegexLiteral');
function match_RegexLiteral ($stack = []) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, \null);
	$_10 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === 'r') {
			$this->pos += 1;
			$result["text"] .= 'r';
		}
		else { $_10 = \false; break; }
		$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_10 = \false; break; }
		$_10 = \true; break;
	}
	while(0);
	if( $_10 === \true ) { return $this->finalise($result); }
	if( $_10 === \false) { return \false; }
}


/* Nothing: "" */
protected $match_Nothing_typestack = array('Nothing');
function match_Nothing ($stack = []) {
	$matchrule = "Nothing"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->literal( '' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:NullLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = []) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, \null);
	$_28 = \null;
	do {
		$res_13 = $result;
		$pos_13 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_28 = \true; break;
		}
		$result = $res_13;
		$this->pos = $pos_13;
		$_26 = \null;
		do {
			$res_15 = $result;
			$pos_15 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_26 = \true; break;
			}
			$result = $res_15;
			$this->pos = $pos_15;
			$_24 = \null;
			do {
				$res_17 = $result;
				$pos_17 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_24 = \true; break;
				}
				$result = $res_17;
				$this->pos = $pos_17;
				$_22 = \null;
				do {
					$res_19 = $result;
					$pos_19 = $this->pos;
					$matcher = 'match_'.'NullLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_22 = \true; break;
					}
					$result = $res_19;
					$this->pos = $pos_19;
					$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_22 = \true; break;
					}
					$result = $res_19;
					$this->pos = $pos_19;
					$_22 = \false; break;
				}
				while(0);
				if( $_22 === \true ) { $_24 = \true; break; }
				$result = $res_17;
				$this->pos = $pos_17;
				$_24 = \false; break;
			}
			while(0);
			if( $_24 === \true ) { $_26 = \true; break; }
			$result = $res_15;
			$this->pos = $pos_15;
			$_26 = \false; break;
		}
		while(0);
		if( $_26 === \true ) { $_28 = \true; break; }
		$result = $res_13;
		$this->pos = $pos_13;
		$_28 = \false; break;
	}
	while(0);
	if( $_28 === \true ) { return $this->finalise($result); }
	if( $_28 === \false) { return \false; }
}


/* VariableName: / ([a-zA-Z_][a-zA-Z0-9_]*) / */
protected $match_VariableName_typestack = array('VariableName');
function match_VariableName ($stack = []) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ ([a-zA-Z_][a-zA-Z0-9_]*) /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* Variable: core:VariableName */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = []) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, \null);
	$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
	if ($subres !== \false) {
		$this->store( $result, $subres, "core" );
		return $this->finalise($result);
	}
	else { return \false; }
}


/* UnaryOpVariable: ( pre:UnaryOperator core:Variable ) | ( core:Variable post:UnaryOperator ) */
protected $match_UnaryOpVariable_typestack = array('UnaryOpVariable');
function match_UnaryOpVariable ($stack = []) {
	$matchrule = "UnaryOpVariable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_41 = \null;
	do {
		$res_32 = $result;
		$pos_32 = $this->pos;
		$_35 = \null;
		do {
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "pre" );
			}
			else { $_35 = \false; break; }
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_35 = \false; break; }
			$_35 = \true; break;
		}
		while(0);
		if( $_35 === \true ) { $_41 = \true; break; }
		$result = $res_32;
		$this->pos = $pos_32;
		$_39 = \null;
		do {
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "core" );
			}
			else { $_39 = \false; break; }
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "post" );
			}
			else { $_39 = \false; break; }
			$_39 = \true; break;
		}
		while(0);
		if( $_39 === \true ) { $_41 = \true; break; }
		$result = $res_32;
		$this->pos = $pos_32;
		$_41 = \false; break;
	}
	while(0);
	if( $_41 === \true ) { return $this->finalise($result); }
	if( $_41 === \false) { return \false; }
}


/* AnonymousFunction: "function" __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block | "(" __ args:FunctionDefinitionArgumentList? __ ")" __ "=>" __ body:Block */
protected $match_AnonymousFunction_typestack = array('AnonymousFunction');
function match_AnonymousFunction ($stack = []) {
	$matchrule = "AnonymousFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_66 = \null;
	do {
		$res_43 = $result;
		$pos_43 = $this->pos;
		$_53 = \null;
		do {
			if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_53 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_53 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			$res_48 = $result;
			$pos_48 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_48;
				$this->pos = $pos_48;
				unset( $res_48 );
				unset( $pos_48 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_53 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_53 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_53 = \false; break; }
			$_53 = \true; break;
		}
		while(0);
		if( $_53 === \true ) { $_66 = \true; break; }
		$result = $res_43;
		$this->pos = $pos_43;
		$_64 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_64 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			$res_57 = $result;
			$pos_57 = $this->pos;
			$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "args" );
			}
			else {
				$result = $res_57;
				$this->pos = $pos_57;
				unset( $res_57 );
				unset( $pos_57 );
			}
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_64 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			if (( $subres = $this->literal( '=>' ) ) !== \false) { $result["text"] .= $subres; }
			else { $_64 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_64 = \false; break; }
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "body" );
			}
			else { $_64 = \false; break; }
			$_64 = \true; break;
		}
		while(0);
		if( $_64 === \true ) { $_66 = \true; break; }
		$result = $res_43;
		$this->pos = $pos_43;
		$_66 = \false; break;
	}
	while(0);
	if( $_66 === \true ) { return $this->finalise($result); }
	if( $_66 === \false) { return \false; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = []) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, \null);
	$_75 = \null;
	do {
		$res_72 = $result;
		$pos_72 = $this->pos;
		$_71 = \null;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "key" );
			}
			else { $_71 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_71 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_71 = \false; break; }
			$_71 = \true; break;
		}
		while(0);
		if( $_71 === \false) {
			$result = $res_72;
			$this->pos = $pos_72;
			unset( $res_72 );
			unset( $pos_72 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_75 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "value" );
		}
		else { $_75 = \false; break; }
		$_75 = \true; break;
	}
	while(0);
	if( $_75 === \true ) { return $this->finalise($result); }
	if( $_75 === \false) { return \false; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ ( "," __ )? "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = []) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_94 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_94 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_94 = \false; break; }
		$res_87 = $result;
		$pos_87 = $this->pos;
		$_86 = \null;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "items" );
			}
			else { $_86 = \false; break; }
			while (\true) {
				$res_85 = $result;
				$pos_85 = $this->pos;
				$_84 = \null;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_84 = \false; break; }
					if (\substr($this->string,$this->pos,1) === ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_84 = \false; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) { $this->store( $result, $subres ); }
					else { $_84 = \false; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "items" );
					}
					else { $_84 = \false; break; }
					$_84 = \true; break;
				}
				while(0);
				if( $_84 === \false) {
					$result = $res_85;
					$this->pos = $pos_85;
					unset( $res_85 );
					unset( $pos_85 );
					break;
				}
			}
			$_86 = \true; break;
		}
		while(0);
		if( $_86 === \false) {
			$result = $res_87;
			$this->pos = $pos_87;
			unset( $res_87 );
			unset( $pos_87 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_94 = \false; break; }
		$res_92 = $result;
		$pos_92 = $this->pos;
		$_91 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_91 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_91 = \false; break; }
			$_91 = \true; break;
		}
		while(0);
		if( $_91 === \false) {
			$result = $res_92;
			$this->pos = $pos_92;
			unset( $res_92 );
			unset( $pos_92 );
		}
		if (\substr($this->string,$this->pos,1) === ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_94 = \false; break; }
		$_94 = \true; break;
	}
	while(0);
	if( $_94 === \true ) { return $this->finalise($result); }
	if( $_94 === \false) { return \false; }
}


/* Value: skip:Literal | skip:UnaryOpVariable | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = []) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, \null);
	$_107 = \null;
	do {
		$res_96 = $result;
		$pos_96 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_107 = \true; break;
		}
		$result = $res_96;
		$this->pos = $pos_96;
		$_105 = \null;
		do {
			$res_98 = $result;
			$pos_98 = $this->pos;
			$matcher = 'match_'.'UnaryOpVariable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_105 = \true; break;
			}
			$result = $res_98;
			$this->pos = $pos_98;
			$_103 = \null;
			do {
				$res_100 = $result;
				$pos_100 = $this->pos;
				$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_103 = \true; break;
				}
				$result = $res_100;
				$this->pos = $pos_100;
				$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_103 = \true; break;
				}
				$result = $res_100;
				$this->pos = $pos_100;
				$_103 = \false; break;
			}
			while(0);
			if( $_103 === \true ) { $_105 = \true; break; }
			$result = $res_98;
			$this->pos = $pos_98;
			$_105 = \false; break;
		}
		while(0);
		if( $_105 === \true ) { $_107 = \true; break; }
		$result = $res_96;
		$this->pos = $pos_96;
		$_107 = \false; break;
	}
	while(0);
	if( $_107 === \true ) { return $this->finalise($result); }
	if( $_107 === \false) { return \false; }
}


/* VariableVector: core:Variable vector:Vector */
protected $match_VariableVector_typestack = array('VariableVector');
function match_VariableVector ($stack = []) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, \null);
	$_111 = \null;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_111 = \false; break; }
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "vector" );
		}
		else { $_111 = \false; break; }
		$_111 = \true; break;
	}
	while(0);
	if( $_111 === \true ) { return $this->finalise($result); }
	if( $_111 === \false) { return \false; }
}


/* Vector: ( "[" __ ( arrayKey:Expression | arrayKey:Nothing ) __ "]" ) vector:Vector? */
protected $match_Vector_typestack = array('Vector');
function match_Vector ($stack = []) {
	$matchrule = "Vector"; $result = $this->construct($matchrule, $matchrule, \null);
	$_127 = \null;
	do {
		$_124 = \null;
		do {
			if (\substr($this->string,$this->pos,1) === '[') {
				$this->pos += 1;
				$result["text"] .= '[';
			}
			else { $_124 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_124 = \false; break; }
			$_120 = \null;
			do {
				$_118 = \null;
				do {
					$res_115 = $result;
					$pos_115 = $this->pos;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "arrayKey" );
						$_118 = \true; break;
					}
					$result = $res_115;
					$this->pos = $pos_115;
					$matcher = 'match_'.'Nothing'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "arrayKey" );
						$_118 = \true; break;
					}
					$result = $res_115;
					$this->pos = $pos_115;
					$_118 = \false; break;
				}
				while(0);
				if( $_118 === \false) { $_120 = \false; break; }
				$_120 = \true; break;
			}
			while(0);
			if( $_120 === \false) { $_124 = \false; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) { $this->store( $result, $subres ); }
			else { $_124 = \false; break; }
			if (\substr($this->string,$this->pos,1) === ']') {
				$this->pos += 1;
				$result["text"] .= ']';
			}
			else { $_124 = \false; break; }
			$_124 = \true; break;
		}
		while(0);
		if( $_124 === \false) { $_127 = \false; break; }
		$res_126 = $result;
		$pos_126 = $this->pos;
		$matcher = 'match_'.'Vector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "vector" );
		}
		else {
			$result = $res_126;
			$this->pos = $pos_126;
			unset( $res_126 );
			unset( $pos_126 );
		}
		$_127 = \true; break;
	}
	while(0);
	if( $_127 === \true ) { return $this->finalise($result); }
	if( $_127 === \false) { return \false; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = array('Mutable');
function match_Mutable ($stack = []) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, \null);
	$_132 = \null;
	do {
		$res_129 = $result;
		$pos_129 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_132 = \true; break;
		}
		$result = $res_129;
		$this->pos = $pos_129;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_132 = \true; break;
		}
		$result = $res_129;
		$this->pos = $pos_129;
		$_132 = \false; break;
	}
	while(0);
	if( $_132 === \true ) { return $this->finalise($result); }
	if( $_132 === \false) { return \false; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = array('ObjectResolutionOperator');
function match_ObjectResolutionOperator ($stack = []) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = []) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_138 = \null;
	do {
		$res_135 = $result;
		$pos_135 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_138 = \true; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		if (\substr($this->string,$this->pos,1) === '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_138 = \true; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		$_138 = \false; break;
	}
	while(0);
	if( $_138 === \true ) { return $this->finalise($result); }
	if( $_138 === \false) { return \false; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = []) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_143 = \null;
	do {
		$res_140 = $result;
		$pos_140 = $this->pos;
		if (\substr($this->string,$this->pos,1) === '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_143 = \true; break;
		}
		$result = $res_140;
		$this->pos = $pos_140;
		if (\substr($this->string,$this->pos,1) === '/') {
			$this->pos += 1;
			$result["text"] .= '/';
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


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = []) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = []) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_165 = \null;
	do {
		$res_146 = $result;
		$pos_146 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== \false) {
			$result["text"] .= $subres;
			$_165 = \true; break;
		}
		$result = $res_146;
		$this->pos = $pos_146;
		$_163 = \null;
		do {
			$res_148 = $result;
			$pos_148 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== \false) {
				$result["text"] .= $subres;
				$_163 = \true; break;
			}
			$result = $res_148;
			$this->pos = $pos_148;
			$_161 = \null;
			do {
				$res_150 = $result;
				$pos_150 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== \false) {
					$result["text"] .= $subres;
					$_161 = \true; break;
				}
				$result = $res_150;
				$this->pos = $pos_150;
				$_159 = \null;
				do {
					$res_152 = $result;
					$pos_152 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== \false) {
						$result["text"] .= $subres;
						$_159 = \true; break;
					}
					$result = $res_152;
					$this->pos = $pos_152;
					$_157 = \null;
					do {
						$res_154 = $result;
						$pos_154 = $this->pos;
						if (\substr($this->string,$this->pos,1) === '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_157 = \true; break;
						}
						$result = $res_154;
						$this->pos = $pos_154;
						if (\substr($this->string,$this->pos,1) === '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_157 = \true; break;
						}
						$result = $res_154;
						$this->pos = $pos_154;
						$_157 = \false; break;
					}
					while(0);
					if( $_157 === \true ) { $_159 = \true; break; }
					$result = $res_152;
					$this->pos = $pos_152;
					$_159 = \false; break;
				}
				while(0);
				if( $_159 === \true ) { $_161 = \true; break; }
				$result = $res_150;
				$this->pos = $pos_150;
				$_161 = \false; break;
			}
			while(0);
			if( $_161 === \true ) { $_163 = \true; break; }
			$result = $res_148;
			$this->pos = $pos_148;
			$_163 = \false; break;
		}
		while(0);
		if( $_163 === \true ) { $_165 = \true; break; }
		$result = $res_146;
		$this->pos = $pos_146;
		$_165 = \false; break;
	}
	while(0);
	if( $_165 === \true ) { return $this->finalise($result); }
	if( $_165 === \false) { return \false; }
}


/* AndOperator: "and" */
protected $match_AndOperator_typestack = array('AndOperator');
function match_AndOperator ($stack = []) {
	$matchrule = "AndOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->literal( 'and' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* OrOperator: "or" */
protected $match_OrOperator_typestack = array('OrOperator');
function match_OrOperator ($stack = []) {
	$matchrule = "OrOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->literal( 'or' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NegationOperator: "!" */
protected $match_NegationOperator_typestack = array('NegationOperator');
function match_NegationOperator ($stack = []) {
	$matchrule = "NegationOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	if (\substr($this->string,$this->pos,1) === '!') {
		$this->pos += 1;
		$result["text"] .= '!';
		return $this->finalise($result);
	}
	else { return \false; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = []) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, \null);
	$_173 = \null;
	do {
		$res_170 = $result;
		$pos_170 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== \false) {
			$result["text"] .= $subres;
			$_173 = \true; break;
		}
		$result = $res_170;
		$this->pos = $pos_170;
		if (( $subres = $this->literal( '--' ) ) !== \false) {
			$result["text"] .= $subres;
			$_173 = \true; break;
		}
		$result = $res_170;
		$this->pos = $pos_170;
		$_173 = \false; break;
	}
	while(0);
	if( $_173 === \true ) { return $this->finalise($result); }
	if( $_173 === \false) { return \false; }
}


/* Expression: skip:AnonymousFunction | skip:Assignment | skip:LogicalOr */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = []) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, \null);
	$_182 = \null;
	do {
		$res_175 = $result;
		$pos_175 = $this->pos;
		$matcher = 'match_'.'AnonymousFunction'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_182 = \true; break;
		}
		$result = $res_175;
		$this->pos = $pos_175;
		$_180 = \null;
		do {
			$res_177 = $result;
			$pos_177 = $this->pos;
			$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
				$_180 = \true; break;
			}
			$result = $res_177;
			$this->pos = $pos_177;
			$matcher = 'match_'.'LogicalOr'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
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
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = []) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, \null);
	$_189 = \null;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_189 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_189 = \false; break; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_189 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_189 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "right" );
		}
		else { $_189 = \false; break; }
		$_189 = \true; break;
	}
	while(0);
	if( $_189 === \true ) { return $this->finalise($result); }
	if( $_189 === \false) { return \false; }
}


/* LogicalOr: operands:LogicalAnd ( ] ops:OrOperator ] operands:LogicalAnd )* */
protected $match_LogicalOr_typestack = array('LogicalOr');
function match_LogicalOr ($stack = []) {
	$matchrule = "LogicalOr"; $result = $this->construct($matchrule, $matchrule, \null);
	$_198 = \null;
	do {
		$matcher = 'match_'.'LogicalAnd'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_198 = \false; break; }
		while (\true) {
			$res_197 = $result;
			$pos_197 = $this->pos;
			$_196 = \null;
			do {
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				else { $_196 = \false; break; }
				$matcher = 'match_'.'OrOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_196 = \false; break; }
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				else { $_196 = \false; break; }
				$matcher = 'match_'.'LogicalAnd'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
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
protected $match_LogicalAnd_typestack = array('LogicalAnd');
function match_LogicalAnd ($stack = []) {
	$matchrule = "LogicalAnd"; $result = $this->construct($matchrule, $matchrule, \null);
	$_207 = \null;
	do {
		$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_207 = \false; break; }
		while (\true) {
			$res_206 = $result;
			$pos_206 = $this->pos;
			$_205 = \null;
			do {
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				else { $_205 = \false; break; }
				$matcher = 'match_'.'AndOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_205 = \false; break; }
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				else { $_205 = \false; break; }
				$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
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
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = []) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, \null);
	$_216 = \null;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_216 = \false; break; }
		while (\true) {
			$res_215 = $result;
			$pos_215 = $this->pos;
			$_214 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_214 = \false; break; }
				$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_214 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_214 = \false; break; }
				$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
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
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = []) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_225 = \null;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_225 = \false; break; }
		while (\true) {
			$res_224 = $result;
			$pos_224 = $this->pos;
			$_223 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_223 = \false; break; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_223 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_223 = \false; break; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
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
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = []) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, \null);
	$_234 = \null;
	do {
		$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_234 = \false; break; }
		while (\true) {
			$res_233 = $result;
			$pos_233 = $this->pos;
			$_232 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_232 = \false; break; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_232 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_232 = \false; break; }
				$matcher = 'match_'.'Negation'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "operands" );
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
protected $match_Negation_typestack = array('Negation');
function match_Negation ($stack = []) {
	$matchrule = "Negation"; $result = $this->construct($matchrule, $matchrule, \null);
	$_240 = \null;
	do {
		while (\true) {
			$res_238 = $result;
			$pos_238 = $this->pos;
			$_237 = \null;
			do {
				$matcher = 'match_'.'NegationOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "nots" );
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
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "core" );
		}
		else { $_240 = \false; break; }
		$_240 = \true; break;
	}
	while(0);
	if( $_240 === \true ) { return $this->finalise($result); }
	if( $_240 === \false) { return \false; }
}


/* Operand: ( ( "(" __ core:Expression __ ")" | core:Value ) chain:Chain? ) | skip:Value */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = []) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, \null);
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
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
						else { $_249 = \false; break; }
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "core" );
						}
						else { $_249 = \false; break; }
						$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres );
						}
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
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
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
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "chain" );
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
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
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
protected $match_Chain_typestack = array('Chain');
function match_Chain ($stack = []) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, \null);
	$_274 = \null;
	do {
		$_271 = \null;
		do {
			$_269 = \null;
			do {
				$res_262 = $result;
				$pos_262 = $this->pos;
				$matcher = 'match_'.'Dereference'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "core" );
					$_269 = \true; break;
				}
				$result = $res_262;
				$this->pos = $pos_262;
				$_267 = \null;
				do {
					$res_264 = $result;
					$pos_264 = $this->pos;
					$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
						$_267 = \true; break;
					}
					$result = $res_264;
					$this->pos = $pos_264;
					$matcher = 'match_'.'ChainedFunction'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "core" );
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
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "chain" );
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
protected $match_Dereference_typestack = array('Dereference');
function match_Dereference ($stack = []) {
	$matchrule = "Dereference"; $result = $this->construct($matchrule, $matchrule, \null);
	$_281 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_281 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_281 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "key" );
		}
		else { $_281 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
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
protected $match_Invocation_typestack = array('Invocation');
function match_Invocation ($stack = []) {
	$matchrule = "Invocation"; $result = $this->construct($matchrule, $matchrule, \null);
	$_288 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_288 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_288 = \false; break; }
		$res_285 = $result;
		$pos_285 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_285;
			$this->pos = $pos_285;
			unset( $res_285 );
			unset( $pos_285 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
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
protected $match_ChainedFunction_typestack = array('ChainedFunction');
function match_ChainedFunction ($stack = []) {
	$matchrule = "ChainedFunction"; $result = $this->construct($matchrule, $matchrule, \null);
	$_293 = \null;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_293 = \false; break; }
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "fn" );
		}
		else { $_293 = \false; break; }
		$matcher = 'match_'.'Invocation'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "invo" );
		}
		else { $_293 = \false; break; }
		$_293 = \true; break;
	}
	while(0);
	if( $_293 === \true ) { return $this->finalise($result); }
	if( $_293 === \false) { return \false; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = array('ArgumentList');
function match_ArgumentList ($stack = []) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_302 = \null;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else { $_302 = \false; break; }
		while (\true) {
			$res_301 = $result;
			$pos_301 = $this->pos;
			$_300 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_300 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_300 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_300 = \false; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "args" );
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
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = []) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, \null);
	$_311 = \null;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_311 = \false; break; }
		while (\true) {
			$res_310 = $result;
			$pos_310 = $this->pos;
			$_309 = \null;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_309 = \false; break; }
				if (\substr($this->string,$this->pos,1) === ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_309 = \false; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_309 = \false; break; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
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


/* FunctionDefinition: "function" [ function:VariableName __ "(" __ args:FunctionDefinitionArgumentList? __ ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = []) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, \null);
	$_324 = \null;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_324 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_324 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "function" );
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		$res_319 = $result;
		$pos_319 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_319;
			$this->pos = $pos_319;
			unset( $res_319 );
			unset( $pos_319 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_324 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_324 = \false; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "body" );
		}
		else { $_324 = \false; break; }
		$_324 = \true; break;
	}
	while(0);
	if( $_324 === \true ) { return $this->finalise($result); }
	if( $_324 === \false) { return \false; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = []) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_337 = \null;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_337 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_337 = \false; break; }
		$_335 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_335 = \false; break; }
			$_335 = \true; break;
		}
		while(0);
		if( $_335 === \false) { $_337 = \false; break; }
		$_337 = \true; break;
	}
	while(0);
	if( $_337 === \true ) { return $this->finalise($result); }
	if( $_337 === \false) { return \false; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = []) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_350 = \null;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_350 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_350 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_350 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_350 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_350 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_350 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_350 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_350 = \false; break; }
		$_348 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_348 = \false; break; }
			$_348 = \true; break;
		}
		while(0);
		if( $_348 === \false) { $_350 = \false; break; }
		$_350 = \true; break;
	}
	while(0);
	if( $_350 === \true ) { return $this->finalise($result); }
	if( $_350 === \false) { return \false; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = []) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_367 = \null;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_367 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_367 = \false; break; }
		if (\substr($this->string,$this->pos,1) === '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_367 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_367 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "left" );
		}
		else { $_367 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_367 = \false; break; }
		if (( $subres = $this->literal( 'as' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_367 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_367 = \false; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "item" );
		}
		else { $_367 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_367 = \false; break; }
		if (\substr($this->string,$this->pos,1) === ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_367 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_367 = \false; break; }
		$_365 = \null;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "right" );
			}
			else { $_365 = \false; break; }
			$_365 = \true; break;
		}
		while(0);
		if( $_365 === \false) { $_367 = \false; break; }
		$_367 = \true; break;
	}
	while(0);
	if( $_367 === \true ) { return $this->finalise($result); }
	if( $_367 === \false) { return \false; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = []) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_372 = \null;
	do {
		$res_369 = $result;
		$pos_369 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_372 = \true; break;
		}
		$result = $res_369;
		$this->pos = $pos_369;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "skip" );
			$_372 = \true; break;
		}
		$result = $res_369;
		$this->pos = $pos_369;
		$_372 = \false; break;
	}
	while(0);
	if( $_372 === \true ) { return $this->finalise($result); }
	if( $_372 === \false) { return \false; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = []) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_377 = \null;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_377 = \false; break; }
		if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
		else { $_377 = \false; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_377 = \false; break; }
		$_377 = \true; break;
	}
	while(0);
	if( $_377 === \true ) { return $this->finalise($result); }
	if( $_377 === \false) { return \false; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = []) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_384 = \null;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== \false) { $result["text"] .= $subres; }
		else { $_384 = \false; break; }
		$res_383 = $result;
		$pos_383 = $this->pos;
		$_382 = \null;
		do {
			if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
			else { $_382 = \false; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_382 = \false; break; }
			$_382 = \true; break;
		}
		while(0);
		if( $_382 === \false) {
			$result = $res_383;
			$this->pos = $pos_383;
			unset( $res_383 );
			unset( $pos_383 );
		}
		$_384 = \true; break;
	}
	while(0);
	if( $_384 === \true ) { return $this->finalise($result); }
	if( $_384 === \false) { return \false; }
}


/* BlockStatements: &/[A-Za-z]/ ( skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition ) */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = []) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, \null);
	$_402 = \null;
	do {
		$res_386 = $result;
		$pos_386 = $this->pos;
		if (( $subres = $this->rx( '/[A-Za-z]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_386;
			$this->pos = $pos_386;
		}
		else {
			$result = $res_386;
			$this->pos = $pos_386;
			$_402 = \false; break;
		}
		$_400 = \null;
		do {
			$_398 = \null;
			do {
				$res_387 = $result;
				$pos_387 = $this->pos;
				$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_398 = \true; break;
				}
				$result = $res_387;
				$this->pos = $pos_387;
				$_396 = \null;
				do {
					$res_389 = $result;
					$pos_389 = $this->pos;
					$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_396 = \true; break;
					}
					$result = $res_389;
					$this->pos = $pos_389;
					$_394 = \null;
					do {
						$res_391 = $result;
						$pos_391 = $this->pos;
						$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_394 = \true; break;
						}
						$result = $res_391;
						$this->pos = $pos_391;
						$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
						if ($subres !== \false) {
							$this->store( $result, $subres, "skip" );
							$_394 = \true; break;
						}
						$result = $res_391;
						$this->pos = $pos_391;
						$_394 = \false; break;
					}
					while(0);
					if( $_394 === \true ) { $_396 = \true; break; }
					$result = $res_389;
					$this->pos = $pos_389;
					$_396 = \false; break;
				}
				while(0);
				if( $_396 === \true ) { $_398 = \true; break; }
				$result = $res_387;
				$this->pos = $pos_387;
				$_398 = \false; break;
			}
			while(0);
			if( $_398 === \false) { $_400 = \false; break; }
			$_400 = \true; break;
		}
		while(0);
		if( $_400 === \false) { $_402 = \false; break; }
		$_402 = \true; break;
	}
	while(0);
	if( $_402 === \true ) { return $this->finalise($result); }
	if( $_402 === \false) { return \false; }
}


/* Statement: !/[\s\{\};]/ ( skip:BlockStatements | skip:CommandStatements | skip:Expression ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = []) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, \null);
	$_416 = \null;
	do {
		$res_404 = $result;
		$pos_404 = $this->pos;
		if (( $subres = $this->rx( '/[\s\{\};]/' ) ) !== \false) {
			$result["text"] .= $subres;
			$result = $res_404;
			$this->pos = $pos_404;
			$_416 = \false; break;
		}
		else {
			$result = $res_404;
			$this->pos = $pos_404;
		}
		$_414 = \null;
		do {
			$_412 = \null;
			do {
				$res_405 = $result;
				$pos_405 = $this->pos;
				$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) {
					$this->store( $result, $subres, "skip" );
					$_412 = \true; break;
				}
				$result = $res_405;
				$this->pos = $pos_405;
				$_410 = \null;
				do {
					$res_407 = $result;
					$pos_407 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
						$_410 = \true; break;
					}
					$result = $res_407;
					$this->pos = $pos_407;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
					if ($subres !== \false) {
						$this->store( $result, $subres, "skip" );
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
			if( $_412 === \false) { $_414 = \false; break; }
			$_414 = \true; break;
		}
		while(0);
		if( $_414 === \false) { $_416 = \false; break; }
		$_416 = \true; break;
	}
	while(0);
	if( $_416 === \true ) { return $this->finalise($result); }
	if( $_416 === \false) { return \false; }
}


/* Block: "{" __ ( skip:Program )? "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = []) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, \null);
	$_424 = \null;
	do {
		if (\substr($this->string,$this->pos,1) === '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_424 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_424 = \false; break; }
		$res_422 = $result;
		$pos_422 = $this->pos;
		$_421 = \null;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
			if ($subres !== \false) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_421 = \false; break; }
			$_421 = \true; break;
		}
		while(0);
		if( $_421 === \false) {
			$result = $res_422;
			$this->pos = $pos_422;
			unset( $res_422 );
			unset( $pos_422 );
		}
		if (\substr($this->string,$this->pos,1) === '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_424 = \false; break; }
		$_424 = \true; break;
	}
	while(0);
	if( $_424 === \true ) { return $this->finalise($result); }
	if( $_424 === \false) { return \false; }
}


/* __: / [\s\n]* / */
protected $match____typestack = array('__');
function match___ ($stack = []) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ [\s\n]* /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* NL: / (?:\/\/[^\n]*)?\n / */
protected $match_NL_typestack = array('NL');
function match_NL ($stack = []) {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule, \null);
	if (( $subres = $this->rx( '/ (?:\/\/[^\n]*)?\n /' ) ) !== \false) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return \false; }
}


/* SEP: ";" | NL */
protected $match_SEP_typestack = array('SEP');
function match_SEP ($stack = []) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule, \null);
	$_431 = \null;
	do {
		$res_428 = $result;
		$pos_428 = $this->pos;
		if (\substr($this->string,$this->pos,1) === ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_431 = \true; break;
		}
		$result = $res_428;
		$this->pos = $pos_428;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) {
			$this->store( $result, $subres );
			$_431 = \true; break;
		}
		$result = $res_428;
		$this->pos = $pos_428;
		$_431 = \false; break;
	}
	while(0);
	if( $_431 === \true ) { return $this->finalise($result); }
	if( $_431 === \false) { return \false; }
}


/* Program: ( !/$/ __ Statement? > SEP )+ __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = []) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, \null);
	$_441 = \null;
	do {
		$count = 0;
		while (\true) {
			$res_439 = $result;
			$pos_439 = $this->pos;
			$_438 = \null;
			do {
				$res_433 = $result;
				$pos_433 = $this->pos;
				if (( $subres = $this->rx( '/$/' ) ) !== \false) {
					$result["text"] .= $subres;
					$result = $res_433;
					$this->pos = $pos_433;
					$_438 = \false; break;
				}
				else {
					$result = $res_433;
					$this->pos = $pos_433;
				}
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_438 = \false; break; }
				$res_435 = $result;
				$pos_435 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else {
					$result = $res_435;
					$this->pos = $pos_435;
					unset( $res_435 );
					unset( $pos_435 );
				}
				if (( $subres = $this->whitespace(  ) ) !== \false) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
				if ($subres !== \false) { $this->store( $result, $subres ); }
				else { $_438 = \false; break; }
				$_438 = \true; break;
			}
			while(0);
			if( $_438 === \false) {
				$result = $res_439;
				$this->pos = $pos_439;
				unset( $res_439 );
				unset( $pos_439 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_441 = \false; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(\array_merge($stack, array($result))) ) );
		if ($subres !== \false) { $this->store( $result, $subres ); }
		else { $_441 = \false; break; }
		$_441 = \true; break;
	}
	while(0);
	if( $_441 === \true ) { return $this->finalise($result); }
	if( $_441 === \false) { return \false; }
}




}
