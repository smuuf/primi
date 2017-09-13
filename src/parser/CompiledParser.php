<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Packrat {

    // Add these properties so PHPStan doesn't complain for undefined properties.

    /** @var int **/
    public $pos;

    /** @var string **/
    public $string;

/* StringLiteral: /("(.|\n)*?"|'(.|\n)*?')/ */
protected $match_StringLiteral_typestack = array('StringLiteral');
function match_StringLiteral ($stack = array()) {
	$matchrule = "StringLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/("(.|\n)*?"|\'(.|\n)*?\')/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* NumberLiteral: /-?\d+(\.\d+)?/ */
protected $match_NumberLiteral_typestack = array('NumberLiteral');
function match_NumberLiteral ($stack = array()) {
	$matchrule = "NumberLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/-?\d+(\.\d+)?/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* BoolLiteral: "true" | "false" */
protected $match_BoolLiteral_typestack = array('BoolLiteral');
function match_BoolLiteral ($stack = array()) {
	$matchrule = "BoolLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	$_5 = NULL;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		if (( $subres = $this->literal( 'true' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_5 = TRUE; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		if (( $subres = $this->literal( 'false' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_5 = TRUE; break;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$_5 = FALSE; break;
	}
	while(0);
	if( $_5 === TRUE ) { return $this->finalise($result); }
	if( $_5 === FALSE) { return FALSE; }
}


/* RegexLiteral: "/" /(\\\/|[^\/])+/ "/" */
protected $match_RegexLiteral_typestack = array('RegexLiteral');
function match_RegexLiteral ($stack = array()) {
	$matchrule = "RegexLiteral"; $result = $this->construct($matchrule, $matchrule, null);
	$_10 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = FALSE; break; }
		if (( $subres = $this->rx( '/(\\\\\/|[^\/])+/' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_10 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_10 = FALSE; break; }
		$_10 = TRUE; break;
	}
	while(0);
	if( $_10 === TRUE ) { return $this->finalise($result); }
	if( $_10 === FALSE) { return FALSE; }
}


/* Literal: skip:NumberLiteral | skip:StringLiteral | skip:BoolLiteral | skip:RegexLiteral */
protected $match_Literal_typestack = array('Literal');
function match_Literal ($stack = array()) {
	$matchrule = "Literal"; $result = $this->construct($matchrule, $matchrule, null);
	$_23 = NULL;
	do {
		$res_12 = $result;
		$pos_12 = $this->pos;
		$matcher = 'match_'.'NumberLiteral'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_23 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$_21 = NULL;
		do {
			$res_14 = $result;
			$pos_14 = $this->pos;
			$matcher = 'match_'.'StringLiteral'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_21 = TRUE; break;
			}
			$result = $res_14;
			$this->pos = $pos_14;
			$_19 = NULL;
			do {
				$res_16 = $result;
				$pos_16 = $this->pos;
				$matcher = 'match_'.'BoolLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_19 = TRUE; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$matcher = 'match_'.'RegexLiteral'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_19 = TRUE; break;
				}
				$result = $res_16;
				$this->pos = $pos_16;
				$_19 = FALSE; break;
			}
			while(0);
			if( $_19 === TRUE ) { $_21 = TRUE; break; }
			$result = $res_14;
			$this->pos = $pos_14;
			$_21 = FALSE; break;
		}
		while(0);
		if( $_21 === TRUE ) { $_23 = TRUE; break; }
		$result = $res_12;
		$this->pos = $pos_12;
		$_23 = FALSE; break;
	}
	while(0);
	if( $_23 === TRUE ) { return $this->finalise($result); }
	if( $_23 === FALSE) { return FALSE; }
}


/* VariableCore: /([a-zA-Z_][a-zA-Z0-9_]*)/ */
protected $match_VariableCore_typestack = array('VariableCore');
function match_VariableCore ($stack = array()) {
	$matchrule = "VariableCore"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([a-zA-Z_][a-zA-Z0-9_]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Variable: ( core:VariableCore post:UnaryOperator? ) | ( pre:UnaryOperator? core:VariableCore ) */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = array()) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, null);
	$_35 = NULL;
	do {
		$res_26 = $result;
		$pos_26 = $this->pos;
		$_29 = NULL;
		do {
			$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "core" );
			}
			else { $_29 = FALSE; break; }
			$res_28 = $result;
			$pos_28 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "post" );
			}
			else {
				$result = $res_28;
				$this->pos = $pos_28;
				unset( $res_28 );
				unset( $pos_28 );
			}
			$_29 = TRUE; break;
		}
		while(0);
		if( $_29 === TRUE ) { $_35 = TRUE; break; }
		$result = $res_26;
		$this->pos = $pos_26;
		$_33 = NULL;
		do {
			$res_31 = $result;
			$pos_31 = $this->pos;
			$matcher = 'match_'.'UnaryOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "pre" );
			}
			else {
				$result = $res_31;
				$this->pos = $pos_31;
				unset( $res_31 );
				unset( $pos_31 );
			}
			$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "core" );
			}
			else { $_33 = FALSE; break; }
			$_33 = TRUE; break;
		}
		while(0);
		if( $_33 === TRUE ) { $_35 = TRUE; break; }
		$result = $res_26;
		$this->pos = $pos_26;
		$_35 = FALSE; break;
	}
	while(0);
	if( $_35 === TRUE ) { return $this->finalise($result); }
	if( $_35 === FALSE) { return FALSE; }
}


/* ArrayItem: ( key:Expression > ":" )? > value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = array()) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, null);
	$_44 = NULL;
	do {
		$res_41 = $result;
		$pos_41 = $this->pos;
		$_40 = NULL;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "key" );
			}
			else { $_40 = FALSE; break; }
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_40 = FALSE; break; }
			$_40 = TRUE; break;
		}
		while(0);
		if( $_40 === FALSE) {
			$result = $res_41;
			$this->pos = $pos_41;
			unset( $res_41 );
			unset( $pos_41 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "value" );
		}
		else { $_44 = FALSE; break; }
		$_44 = TRUE; break;
	}
	while(0);
	if( $_44 === TRUE ) { return $this->finalise($result); }
	if( $_44 === FALSE) { return FALSE; }
}


/* ArrayDefinition: "[" SPACE ( items:ArrayItem ( SPACE "," SPACE items:ArrayItem )* )? SPACE "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = array()) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_59 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_59 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_59 = FALSE; break; }
		$res_56 = $result;
		$pos_56 = $this->pos;
		$_55 = NULL;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "items" );
			}
			else { $_55 = FALSE; break; }
			while (true) {
				$res_54 = $result;
				$pos_54 = $this->pos;
				$_53 = NULL;
				do {
					$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_53 = FALSE; break; }
					if (substr($this->string,$this->pos,1) == ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_53 = FALSE; break; }
					$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_53 = FALSE; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "items" );
					}
					else { $_53 = FALSE; break; }
					$_53 = TRUE; break;
				}
				while(0);
				if( $_53 === FALSE) {
					$result = $res_54;
					$this->pos = $pos_54;
					unset( $res_54 );
					unset( $pos_54 );
					break;
				}
			}
			$_55 = TRUE; break;
		}
		while(0);
		if( $_55 === FALSE) {
			$result = $res_56;
			$this->pos = $pos_56;
			unset( $res_56 );
			unset( $pos_56 );
		}
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_59 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_59 = FALSE; break; }
		$_59 = TRUE; break;
	}
	while(0);
	if( $_59 === TRUE ) { return $this->finalise($result); }
	if( $_59 === FALSE) { return FALSE; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_68 = NULL;
	do {
		$res_61 = $result;
		$pos_61 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_68 = TRUE; break;
		}
		$result = $res_61;
		$this->pos = $pos_61;
		$_66 = NULL;
		do {
			$res_63 = $result;
			$pos_63 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_66 = TRUE; break;
			}
			$result = $res_63;
			$this->pos = $pos_63;
			$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_66 = TRUE; break;
			}
			$result = $res_63;
			$this->pos = $pos_63;
			$_66 = FALSE; break;
		}
		while(0);
		if( $_66 === TRUE ) { $_68 = TRUE; break; }
		$result = $res_61;
		$this->pos = $pos_61;
		$_68 = FALSE; break;
	}
	while(0);
	if( $_68 === TRUE ) { return $this->finalise($result); }
	if( $_68 === FALSE) { return FALSE; }
}


/* DereferencableValue: core:Value ( "[" > dereference:Expression > "]" )* */
protected $match_DereferencableValue_typestack = array('DereferencableValue');
function match_DereferencableValue ($stack = array()) {
	$matchrule = "DereferencableValue"; $result = $this->construct($matchrule, $matchrule, null);
	$_78 = NULL;
	do {
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "core" );
		}
		else { $_78 = FALSE; break; }
		while (true) {
			$res_77 = $result;
			$pos_77 = $this->pos;
			$_76 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_76 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_76 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_76 = FALSE; break; }
				$_76 = TRUE; break;
			}
			while(0);
			if( $_76 === FALSE) {
				$result = $res_77;
				$this->pos = $pos_77;
				unset( $res_77 );
				unset( $pos_77 );
				break;
			}
		}
		$_78 = TRUE; break;
	}
	while(0);
	if( $_78 === TRUE ) { return $this->finalise($result); }
	if( $_78 === FALSE) { return FALSE; }
}


/* ObjectResolutionOperator: "." */
protected $match_ObjectResolutionOperator_typestack = array('ObjectResolutionOperator');
function match_ObjectResolutionOperator ($stack = array()) {
	$matchrule = "ObjectResolutionOperator"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '.') {
		$this->pos += 1;
		$result["text"] .= '.';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* AddOperator: "+" | "-" */
protected $match_AddOperator_typestack = array('AddOperator');
function match_AddOperator ($stack = array()) {
	$matchrule = "AddOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_84 = NULL;
	do {
		$res_81 = $result;
		$pos_81 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_84 = TRUE; break;
		}
		$result = $res_81;
		$this->pos = $pos_81;
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_84 = TRUE; break;
		}
		$result = $res_81;
		$this->pos = $pos_81;
		$_84 = FALSE; break;
	}
	while(0);
	if( $_84 === TRUE ) { return $this->finalise($result); }
	if( $_84 === FALSE) { return FALSE; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = array()) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_89 = NULL;
	do {
		$res_86 = $result;
		$pos_86 = $this->pos;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_89 = TRUE; break;
		}
		$result = $res_86;
		$this->pos = $pos_86;
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_89 = TRUE; break;
		}
		$result = $res_86;
		$this->pos = $pos_86;
		$_89 = FALSE; break;
	}
	while(0);
	if( $_89 === TRUE ) { return $this->finalise($result); }
	if( $_89 === FALSE) { return FALSE; }
}


/* AssignmentOperator: "=" | "+=" | "-=" | "*=" | "/=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = array()) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_106 = NULL;
	do {
		$res_91 = $result;
		$pos_91 = $this->pos;
		if (substr($this->string,$this->pos,1) == '=') {
			$this->pos += 1;
			$result["text"] .= '=';
			$_106 = TRUE; break;
		}
		$result = $res_91;
		$this->pos = $pos_91;
		$_104 = NULL;
		do {
			$res_93 = $result;
			$pos_93 = $this->pos;
			if (( $subres = $this->literal( '+=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_104 = TRUE; break;
			}
			$result = $res_93;
			$this->pos = $pos_93;
			$_102 = NULL;
			do {
				$res_95 = $result;
				$pos_95 = $this->pos;
				if (( $subres = $this->literal( '-=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_102 = TRUE; break;
				}
				$result = $res_95;
				$this->pos = $pos_95;
				$_100 = NULL;
				do {
					$res_97 = $result;
					$pos_97 = $this->pos;
					if (( $subres = $this->literal( '*=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_100 = TRUE; break;
					}
					$result = $res_97;
					$this->pos = $pos_97;
					if (( $subres = $this->literal( '/=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_100 = TRUE; break;
					}
					$result = $res_97;
					$this->pos = $pos_97;
					$_100 = FALSE; break;
				}
				while(0);
				if( $_100 === TRUE ) { $_102 = TRUE; break; }
				$result = $res_95;
				$this->pos = $pos_95;
				$_102 = FALSE; break;
			}
			while(0);
			if( $_102 === TRUE ) { $_104 = TRUE; break; }
			$result = $res_93;
			$this->pos = $pos_93;
			$_104 = FALSE; break;
		}
		while(0);
		if( $_104 === TRUE ) { $_106 = TRUE; break; }
		$result = $res_91;
		$this->pos = $pos_91;
		$_106 = FALSE; break;
	}
	while(0);
	if( $_106 === TRUE ) { return $this->finalise($result); }
	if( $_106 === FALSE) { return FALSE; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = array()) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_127 = NULL;
	do {
		$res_108 = $result;
		$pos_108 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_127 = TRUE; break;
		}
		$result = $res_108;
		$this->pos = $pos_108;
		$_125 = NULL;
		do {
			$res_110 = $result;
			$pos_110 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_125 = TRUE; break;
			}
			$result = $res_110;
			$this->pos = $pos_110;
			$_123 = NULL;
			do {
				$res_112 = $result;
				$pos_112 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_123 = TRUE; break;
				}
				$result = $res_112;
				$this->pos = $pos_112;
				$_121 = NULL;
				do {
					$res_114 = $result;
					$pos_114 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_121 = TRUE; break;
					}
					$result = $res_114;
					$this->pos = $pos_114;
					$_119 = NULL;
					do {
						$res_116 = $result;
						$pos_116 = $this->pos;
						if (substr($this->string,$this->pos,1) == '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_119 = TRUE; break;
						}
						$result = $res_116;
						$this->pos = $pos_116;
						if (substr($this->string,$this->pos,1) == '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_119 = TRUE; break;
						}
						$result = $res_116;
						$this->pos = $pos_116;
						$_119 = FALSE; break;
					}
					while(0);
					if( $_119 === TRUE ) { $_121 = TRUE; break; }
					$result = $res_114;
					$this->pos = $pos_114;
					$_121 = FALSE; break;
				}
				while(0);
				if( $_121 === TRUE ) { $_123 = TRUE; break; }
				$result = $res_112;
				$this->pos = $pos_112;
				$_123 = FALSE; break;
			}
			while(0);
			if( $_123 === TRUE ) { $_125 = TRUE; break; }
			$result = $res_110;
			$this->pos = $pos_110;
			$_125 = FALSE; break;
		}
		while(0);
		if( $_125 === TRUE ) { $_127 = TRUE; break; }
		$result = $res_108;
		$this->pos = $pos_108;
		$_127 = FALSE; break;
	}
	while(0);
	if( $_127 === TRUE ) { return $this->finalise($result); }
	if( $_127 === FALSE) { return FALSE; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = array()) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_132 = NULL;
	do {
		$res_129 = $result;
		$pos_129 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_132 = TRUE; break;
		}
		$result = $res_129;
		$this->pos = $pos_129;
		if (( $subres = $this->literal( '--' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_132 = TRUE; break;
		}
		$result = $res_129;
		$this->pos = $pos_129;
		$_132 = FALSE; break;
	}
	while(0);
	if( $_132 === TRUE ) { return $this->finalise($result); }
	if( $_132 === FALSE) { return FALSE; }
}


/* Expression: skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = array()) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_141 = NULL;
	do {
		$res_134 = $result;
		$pos_134 = $this->pos;
		$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_141 = TRUE; break;
		}
		$result = $res_134;
		$this->pos = $pos_134;
		$_139 = NULL;
		do {
			$res_136 = $result;
			$pos_136 = $this->pos;
			$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_139 = TRUE; break;
			}
			$result = $res_136;
			$this->pos = $pos_136;
			$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_139 = TRUE; break;
			}
			$result = $res_136;
			$this->pos = $pos_136;
			$_139 = FALSE; break;
		}
		while(0);
		if( $_139 === TRUE ) { $_141 = TRUE; break; }
		$result = $res_134;
		$this->pos = $pos_134;
		$_141 = FALSE; break;
	}
	while(0);
	if( $_141 === TRUE ) { return $this->finalise($result); }
	if( $_141 === FALSE) { return FALSE; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = array()) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
	$_148 = NULL;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_148 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_148 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_148 = FALSE; break; }
		$_148 = TRUE; break;
	}
	while(0);
	if( $_148 === TRUE ) { return $this->finalise($result); }
	if( $_148 === FALSE) { return FALSE; }
}


/* Assignment: left:Variable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = array()) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, null);
	$_155 = NULL;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_155 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_155 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_155 = FALSE; break; }
		$_155 = TRUE; break;
	}
	while(0);
	if( $_155 === TRUE ) { return $this->finalise($result); }
	if( $_155 === FALSE) { return FALSE; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = array()) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, null);
	$_164 = NULL;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_164 = FALSE; break; }
		while (true) {
			$res_163 = $result;
			$pos_163 = $this->pos;
			$_162 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_162 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_162 = FALSE; break; }
				$_162 = TRUE; break;
			}
			while(0);
			if( $_162 === FALSE) {
				$result = $res_163;
				$this->pos = $pos_163;
				unset( $res_163 );
				unset( $pos_163 );
				break;
			}
		}
		$_164 = TRUE; break;
	}
	while(0);
	if( $_164 === TRUE ) { return $this->finalise($result); }
	if( $_164 === FALSE) { return FALSE; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = array()) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, null);
	$_173 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_173 = FALSE; break; }
		while (true) {
			$res_172 = $result;
			$pos_172 = $this->pos;
			$_171 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_171 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_171 = FALSE; break; }
				$_171 = TRUE; break;
			}
			while(0);
			if( $_171 === FALSE) {
				$result = $res_172;
				$this->pos = $pos_172;
				unset( $res_172 );
				unset( $pos_172 );
				break;
			}
		}
		$_173 = TRUE; break;
	}
	while(0);
	if( $_173 === TRUE ) { return $this->finalise($result); }
	if( $_173 === FALSE) { return FALSE; }
}


/* Operand: ( ( "(" > core:Expression > ")" ) | core:FunctionCall | core:DereferencableValue ) ( ObjectResolutionOperator next:Operand )? */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = array()) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, null);
	$_196 = NULL;
	do {
		$_190 = NULL;
		do {
			$_188 = NULL;
			do {
				$res_175 = $result;
				$pos_175 = $this->pos;
				$_181 = NULL;
				do {
					if (substr($this->string,$this->pos,1) == '(') {
						$this->pos += 1;
						$result["text"] .= '(';
					}
					else { $_181 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_181 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					if (substr($this->string,$this->pos,1) == ')') {
						$this->pos += 1;
						$result["text"] .= ')';
					}
					else { $_181 = FALSE; break; }
					$_181 = TRUE; break;
				}
				while(0);
				if( $_181 === TRUE ) { $_188 = TRUE; break; }
				$result = $res_175;
				$this->pos = $pos_175;
				$_186 = NULL;
				do {
					$res_183 = $result;
					$pos_183 = $this->pos;
					$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_186 = TRUE; break;
					}
					$result = $res_183;
					$this->pos = $pos_183;
					$matcher = 'match_'.'DereferencableValue'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_186 = TRUE; break;
					}
					$result = $res_183;
					$this->pos = $pos_183;
					$_186 = FALSE; break;
				}
				while(0);
				if( $_186 === TRUE ) { $_188 = TRUE; break; }
				$result = $res_175;
				$this->pos = $pos_175;
				$_188 = FALSE; break;
			}
			while(0);
			if( $_188 === FALSE) { $_190 = FALSE; break; }
			$_190 = TRUE; break;
		}
		while(0);
		if( $_190 === FALSE) { $_196 = FALSE; break; }
		$res_195 = $result;
		$pos_195 = $this->pos;
		$_194 = NULL;
		do {
			$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_194 = FALSE; break; }
			$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "next" );
			}
			else { $_194 = FALSE; break; }
			$_194 = TRUE; break;
		}
		while(0);
		if( $_194 === FALSE) {
			$result = $res_195;
			$this->pos = $pos_195;
			unset( $res_195 );
			unset( $pos_195 );
		}
		$_196 = TRUE; break;
	}
	while(0);
	if( $_196 === TRUE ) { return $this->finalise($result); }
	if( $_196 === FALSE) { return FALSE; }
}


/* FunctionCall: function:VariableCore > "(" > args:FunctionCallArgumentList? > ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = array()) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, null);
	$_205 = NULL;
	do {
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_205 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_205 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_202 = $result;
		$pos_202 = $this->pos;
		$matcher = 'match_'.'FunctionCallArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_202;
			$this->pos = $pos_202;
			unset( $res_202 );
			unset( $pos_202 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_205 = FALSE; break; }
		$_205 = TRUE; break;
	}
	while(0);
	if( $_205 === TRUE ) { return $this->finalise($result); }
	if( $_205 === FALSE) { return FALSE; }
}


/* FunctionCallArgumentList: skip:Operand ( > "," > skip:Operand )* */
protected $match_FunctionCallArgumentList_typestack = array('FunctionCallArgumentList');
function match_FunctionCallArgumentList ($stack = array()) {
	$matchrule = "FunctionCallArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_214 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_214 = FALSE; break; }
		while (true) {
			$res_213 = $result;
			$pos_213 = $this->pos;
			$_212 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_212 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_212 = FALSE; break; }
				$_212 = TRUE; break;
			}
			while(0);
			if( $_212 === FALSE) {
				$result = $res_213;
				$this->pos = $pos_213;
				unset( $res_213 );
				unset( $pos_213 );
				break;
			}
		}
		$_214 = TRUE; break;
	}
	while(0);
	if( $_214 === TRUE ) { return $this->finalise($result); }
	if( $_214 === FALSE) { return FALSE; }
}


/* FunctionDefinitionArgumentList: skip:VariableCore ( > "," > skip:VariableCore )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = array()) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_223 = NULL;
	do {
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_223 = FALSE; break; }
		while (true) {
			$res_222 = $result;
			$pos_222 = $this->pos;
			$_221 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_221 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_221 = FALSE; break; }
				$_221 = TRUE; break;
			}
			while(0);
			if( $_221 === FALSE) {
				$result = $res_222;
				$this->pos = $pos_222;
				unset( $res_222 );
				unset( $pos_222 );
				break;
			}
		}
		$_223 = TRUE; break;
	}
	while(0);
	if( $_223 === TRUE ) { return $this->finalise($result); }
	if( $_223 === FALSE) { return FALSE; }
}


/* FunctionDefinition: "function" [ function:VariableCore SPACE "(" > args:FunctionDefinitionArgumentList? > ")" SPACE body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = array()) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_236 = NULL;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_236 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_236 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_236 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_231 = $result;
		$pos_231 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_231;
			$this->pos = $pos_231;
			unset( $res_231 );
			unset( $pos_231 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_236 = FALSE; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "body" );
		}
		else { $_236 = FALSE; break; }
		$_236 = TRUE; break;
	}
	while(0);
	if( $_236 === TRUE ) { return $this->finalise($result); }
	if( $_236 === FALSE) { return FALSE; }
}


/* IfStatement: "if" SPACE "(" > left:Expression > ")" SPACE ( right:Block ) > */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = array()) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_250 = NULL;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_250 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_250 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_250 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_250 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_250 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_250 = FALSE; break; }
		$_247 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_247 = FALSE; break; }
			$_247 = TRUE; break;
		}
		while(0);
		if( $_247 === FALSE) { $_250 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_250 = TRUE; break;
	}
	while(0);
	if( $_250 === TRUE ) { return $this->finalise($result); }
	if( $_250 === FALSE) { return FALSE; }
}


/* WhileStatement: "while" SPACE "(" > left:Expression > ")" SPACE ( right:Block ) > */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = array()) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_264 = NULL;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_264 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_264 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_264 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_264 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_264 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_264 = FALSE; break; }
		$_261 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_261 = FALSE; break; }
			$_261 = TRUE; break;
		}
		while(0);
		if( $_261 === FALSE) { $_264 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_264 = TRUE; break;
	}
	while(0);
	if( $_264 === TRUE ) { return $this->finalise($result); }
	if( $_264 === FALSE) { return FALSE; }
}


/* ForeachStatement: "foreach" SPACE "(" > left:Expression SPACE "as" SPACE item:VariableCore SPACE ")" SPACE ( right:Block ) > */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = array()) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_282 = NULL;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_282 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_282 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_282 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_282 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_282 = FALSE; break; }
		if (( $subres = $this->literal( 'as' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_282 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_282 = FALSE; break; }
		$matcher = 'match_'.'VariableCore'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "item" );
		}
		else { $_282 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_282 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_282 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_282 = FALSE; break; }
		$_279 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_279 = FALSE; break; }
			$_279 = TRUE; break;
		}
		while(0);
		if( $_279 === FALSE) { $_282 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_282 = TRUE; break;
	}
	while(0);
	if( $_282 === TRUE ) { return $this->finalise($result); }
	if( $_282 === FALSE) { return FALSE; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = array()) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_295 = NULL;
	do {
		$res_284 = $result;
		$pos_284 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_295 = TRUE; break;
		}
		$result = $res_284;
		$this->pos = $pos_284;
		$_293 = NULL;
		do {
			$res_286 = $result;
			$pos_286 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_293 = TRUE; break;
			}
			$result = $res_286;
			$this->pos = $pos_286;
			$_291 = NULL;
			do {
				$res_288 = $result;
				$pos_288 = $this->pos;
				$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_291 = TRUE; break;
				}
				$result = $res_288;
				$this->pos = $pos_288;
				$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_291 = TRUE; break;
				}
				$result = $res_288;
				$this->pos = $pos_288;
				$_291 = FALSE; break;
			}
			while(0);
			if( $_291 === TRUE ) { $_293 = TRUE; break; }
			$result = $res_286;
			$this->pos = $pos_286;
			$_293 = FALSE; break;
		}
		while(0);
		if( $_293 === TRUE ) { $_295 = TRUE; break; }
		$result = $res_284;
		$this->pos = $pos_284;
		$_295 = FALSE; break;
	}
	while(0);
	if( $_295 === TRUE ) { return $this->finalise($result); }
	if( $_295 === FALSE) { return FALSE; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = array()) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_300 = NULL;
	do {
		$res_297 = $result;
		$pos_297 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_300 = TRUE; break;
		}
		$result = $res_297;
		$this->pos = $pos_297;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_300 = TRUE; break;
		}
		$result = $res_297;
		$this->pos = $pos_297;
		$_300 = FALSE; break;
	}
	while(0);
	if( $_300 === TRUE ) { return $this->finalise($result); }
	if( $_300 === FALSE) { return FALSE; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = array()) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_305 = NULL;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_305 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_305 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_305 = FALSE; break; }
		$_305 = TRUE; break;
	}
	while(0);
	if( $_305 === TRUE ) { return $this->finalise($result); }
	if( $_305 === FALSE) { return FALSE; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = array()) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_312 = NULL;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_312 = FALSE; break; }
		$res_311 = $result;
		$pos_311 = $this->pos;
		$_310 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			else { $_310 = FALSE; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_310 = FALSE; break; }
			$_310 = TRUE; break;
		}
		while(0);
		if( $_310 === FALSE) {
			$result = $res_311;
			$this->pos = $pos_311;
			unset( $res_311 );
			unset( $pos_311 );
		}
		$_312 = TRUE; break;
	}
	while(0);
	if( $_312 === TRUE ) { return $this->finalise($result); }
	if( $_312 === FALSE) { return FALSE; }
}


/* Statement: ( skip:BlockStatements SPACE ";"? ) | ( ( skip:CommandStatements | skip:Expression ) SPACE ";" ) */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = array()) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, null);
	$_331 = NULL;
	do {
		$res_314 = $result;
		$pos_314 = $this->pos;
		$_318 = NULL;
		do {
			$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_318 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_318 = FALSE; break; }
			$res_317 = $result;
			$pos_317 = $this->pos;
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else {
				$result = $res_317;
				$this->pos = $pos_317;
				unset( $res_317 );
				unset( $pos_317 );
			}
			$_318 = TRUE; break;
		}
		while(0);
		if( $_318 === TRUE ) { $_331 = TRUE; break; }
		$result = $res_314;
		$this->pos = $pos_314;
		$_329 = NULL;
		do {
			$_325 = NULL;
			do {
				$_323 = NULL;
				do {
					$res_320 = $result;
					$pos_320 = $this->pos;
					$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_323 = TRUE; break;
					}
					$result = $res_320;
					$this->pos = $pos_320;
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "skip" );
						$_323 = TRUE; break;
					}
					$result = $res_320;
					$this->pos = $pos_320;
					$_323 = FALSE; break;
				}
				while(0);
				if( $_323 === FALSE) { $_325 = FALSE; break; }
				$_325 = TRUE; break;
			}
			while(0);
			if( $_325 === FALSE) { $_329 = FALSE; break; }
			$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_329 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ';') {
				$this->pos += 1;
				$result["text"] .= ';';
			}
			else { $_329 = FALSE; break; }
			$_329 = TRUE; break;
		}
		while(0);
		if( $_329 === TRUE ) { $_331 = TRUE; break; }
		$result = $res_314;
		$this->pos = $pos_314;
		$_331 = FALSE; break;
	}
	while(0);
	if( $_331 === TRUE ) { return $this->finalise($result); }
	if( $_331 === FALSE) { return FALSE; }
}


/* Block: "{" SPACE ( skip:Program )? SPACE "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = array()) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, null);
	$_340 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_340 = FALSE; break; }
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_340 = FALSE; break; }
		$res_337 = $result;
		$pos_337 = $this->pos;
		$_336 = NULL;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_336 = FALSE; break; }
			$_336 = TRUE; break;
		}
		while(0);
		if( $_336 === FALSE) {
			$result = $res_337;
			$this->pos = $pos_337;
			unset( $res_337 );
			unset( $pos_337 );
		}
		$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_340 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_340 = FALSE; break; }
		$_340 = TRUE; break;
	}
	while(0);
	if( $_340 === TRUE ) { return $this->finalise($result); }
	if( $_340 === FALSE) { return FALSE; }
}


/* SPACE: /([\s\n]*)/ */
protected $match_SPACE_typestack = array('SPACE');
function match_SPACE ($stack = array()) {
	$matchrule = "SPACE"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([\s\n]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Program: > ( SPACE Statement SPACE )+ > */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = array()) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, null);
	$_350 = NULL;
	do {
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$count = 0;
		while (true) {
			$res_348 = $result;
			$pos_348 = $this->pos;
			$_347 = NULL;
			do {
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_347 = FALSE; break; }
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_347 = FALSE; break; }
				$matcher = 'match_'.'SPACE'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_347 = FALSE; break; }
				$_347 = TRUE; break;
			}
			while(0);
			if( $_347 === FALSE) {
				$result = $res_348;
				$this->pos = $pos_348;
				unset( $res_348 );
				unset( $pos_348 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_350 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_350 = TRUE; break;
	}
	while(0);
	if( $_350 === TRUE ) { return $this->finalise($result); }
	if( $_350 === FALSE) { return FALSE; }
}




}
