<?php

namespace Smuuf\Primi;

use hafriedlander\Peg\Parser;

class CompiledParser extends Parser\Basic {

    // Add these properties so PHPStan doesn't complain about undefined properties.

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


/* VariableName: /([a-zA-Z_][a-zA-Z0-9_]*)/ */
protected $match_VariableName_typestack = array('VariableName');
function match_VariableName ($stack = array()) {
	$matchrule = "VariableName"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([a-zA-Z_][a-zA-Z0-9_]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* Variable: ( core:VariableName post:UnaryOperator? ) | ( pre:UnaryOperator? core:VariableName ) */
protected $match_Variable_typestack = array('Variable');
function match_Variable ($stack = array()) {
	$matchrule = "Variable"; $result = $this->construct($matchrule, $matchrule, null);
	$_35 = NULL;
	do {
		$res_26 = $result;
		$pos_26 = $this->pos;
		$_29 = NULL;
		do {
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
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
			$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
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


/* PropertyGetter: core:VariableName */
protected $match_PropertyGetter_typestack = array('PropertyGetter');
function match_PropertyGetter ($stack = array()) {
	$matchrule = "PropertyGetter"; $result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
	$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
	if ($subres !== FALSE) {
		$this->store( $result, $subres, "core" );
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* ArrayItem: ( key:Expression __ ":" )? __ value:Expression ) */
protected $match_ArrayItem_typestack = array('ArrayItem');
function match_ArrayItem ($stack = array()) {
	$matchrule = "ArrayItem"; $result = $this->construct($matchrule, $matchrule, null);
	$_45 = NULL;
	do {
		$res_42 = $result;
		$pos_42 = $this->pos;
		$_41 = NULL;
		do {
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "key" );
			}
			else { $_41 = FALSE; break; }
			$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) { $this->store( $result, $subres ); }
			else { $_41 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == ':') {
				$this->pos += 1;
				$result["text"] .= ':';
			}
			else { $_41 = FALSE; break; }
			$_41 = TRUE; break;
		}
		while(0);
		if( $_41 === FALSE) {
			$result = $res_42;
			$this->pos = $pos_42;
			unset( $res_42 );
			unset( $pos_42 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_45 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "value" );
		}
		else { $_45 = FALSE; break; }
		$_45 = TRUE; break;
	}
	while(0);
	if( $_45 === TRUE ) { return $this->finalise($result); }
	if( $_45 === FALSE) { return FALSE; }
}


/* ArrayDefinition: "[" __ ( items:ArrayItem ( __ "," __ items:ArrayItem )* )? __ "]" */
protected $match_ArrayDefinition_typestack = array('ArrayDefinition');
function match_ArrayDefinition ($stack = array()) {
	$matchrule = "ArrayDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_60 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_60 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_60 = FALSE; break; }
		$res_57 = $result;
		$pos_57 = $this->pos;
		$_56 = NULL;
		do {
			$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "items" );
			}
			else { $_56 = FALSE; break; }
			while (true) {
				$res_55 = $result;
				$pos_55 = $this->pos;
				$_54 = NULL;
				do {
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_54 = FALSE; break; }
					if (substr($this->string,$this->pos,1) == ',') {
						$this->pos += 1;
						$result["text"] .= ',';
					}
					else { $_54 = FALSE; break; }
					$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) { $this->store( $result, $subres ); }
					else { $_54 = FALSE; break; }
					$matcher = 'match_'.'ArrayItem'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "items" );
					}
					else { $_54 = FALSE; break; }
					$_54 = TRUE; break;
				}
				while(0);
				if( $_54 === FALSE) {
					$result = $res_55;
					$this->pos = $pos_55;
					unset( $res_55 );
					unset( $pos_55 );
					break;
				}
			}
			$_56 = TRUE; break;
		}
		while(0);
		if( $_56 === FALSE) {
			$result = $res_57;
			$this->pos = $pos_57;
			unset( $res_57 );
			unset( $pos_57 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_60 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_60 = FALSE; break; }
		$_60 = TRUE; break;
	}
	while(0);
	if( $_60 === TRUE ) { return $this->finalise($result); }
	if( $_60 === FALSE) { return FALSE; }
}


/* Value: skip:Literal | skip:Variable | skip:ArrayDefinition */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value"; $result = $this->construct($matchrule, $matchrule, null);
	$_69 = NULL;
	do {
		$res_62 = $result;
		$pos_62 = $this->pos;
		$matcher = 'match_'.'Literal'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_69 = TRUE; break;
		}
		$result = $res_62;
		$this->pos = $pos_62;
		$_67 = NULL;
		do {
			$res_64 = $result;
			$pos_64 = $this->pos;
			$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_67 = TRUE; break;
			}
			$result = $res_64;
			$this->pos = $pos_64;
			$matcher = 'match_'.'ArrayDefinition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_67 = TRUE; break;
			}
			$result = $res_64;
			$this->pos = $pos_64;
			$_67 = FALSE; break;
		}
		while(0);
		if( $_67 === TRUE ) { $_69 = TRUE; break; }
		$result = $res_62;
		$this->pos = $pos_62;
		$_69 = FALSE; break;
	}
	while(0);
	if( $_69 === TRUE ) { return $this->finalise($result); }
	if( $_69 === FALSE) { return FALSE; }
}


/* DereferencableValue: core:Value ( "[" __ dereference:Expression __ "]" )* */
protected $match_DereferencableValue_typestack = array('DereferencableValue');
function match_DereferencableValue ($stack = array()) {
	$matchrule = "DereferencableValue"; $result = $this->construct($matchrule, $matchrule, null);
	$_79 = NULL;
	do {
		$matcher = 'match_'.'Value'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "core" );
		}
		else { $_79 = FALSE; break; }
		while (true) {
			$res_78 = $result;
			$pos_78 = $this->pos;
			$_77 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_77 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_77 = FALSE; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "dereference" );
				}
				else { $_77 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_77 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_77 = FALSE; break; }
				$_77 = TRUE; break;
			}
			while(0);
			if( $_77 === FALSE) {
				$result = $res_78;
				$this->pos = $pos_78;
				unset( $res_78 );
				unset( $pos_78 );
				break;
			}
		}
		$_79 = TRUE; break;
	}
	while(0);
	if( $_79 === TRUE ) { return $this->finalise($result); }
	if( $_79 === FALSE) { return FALSE; }
}


/* VariableVector: core:Variable ( "[" __ ( vector:Expression | vector:"" ) __ "]" )+ */
protected $match_VariableVector_typestack = array('VariableVector');
function match_VariableVector ($stack = array()) {
	$matchrule = "VariableVector"; $result = $this->construct($matchrule, $matchrule, null);
	$_95 = NULL;
	do {
		$matcher = 'match_'.'Variable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "core" );
		}
		else { $_95 = FALSE; break; }
		$count = 0;
		while (true) {
			$res_94 = $result;
			$pos_94 = $this->pos;
			$_93 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == '[') {
					$this->pos += 1;
					$result["text"] .= '[';
				}
				else { $_93 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_93 = FALSE; break; }
				$_89 = NULL;
				do {
					$_87 = NULL;
					do {
						$res_84 = $result;
						$pos_84 = $this->pos;
						$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres, "vector" );
							$_87 = TRUE; break;
						}
						$result = $res_84;
						$this->pos = $pos_84;
						$stack[] = $result; $result = $this->construct( $matchrule, "vector" ); 
						if (( $subres = $this->literal( '' ) ) !== FALSE) {
							$result["text"] .= $subres;
							$subres = $result; $result = array_pop($stack);
							$this->store( $result, $subres, 'vector' );
							$_87 = TRUE; break;
						}
						else { $result = array_pop($stack); }
						$result = $res_84;
						$this->pos = $pos_84;
						$_87 = FALSE; break;
					}
					while(0);
					if( $_87 === FALSE) { $_89 = FALSE; break; }
					$_89 = TRUE; break;
				}
				while(0);
				if( $_89 === FALSE) { $_93 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_93 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ']') {
					$this->pos += 1;
					$result["text"] .= ']';
				}
				else { $_93 = FALSE; break; }
				$_93 = TRUE; break;
			}
			while(0);
			if( $_93 === FALSE) {
				$result = $res_94;
				$this->pos = $pos_94;
				unset( $res_94 );
				unset( $pos_94 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_95 = FALSE; break; }
		$_95 = TRUE; break;
	}
	while(0);
	if( $_95 === TRUE ) { return $this->finalise($result); }
	if( $_95 === FALSE) { return FALSE; }
}


/* Mutable: skip:VariableVector | skip:VariableName */
protected $match_Mutable_typestack = array('Mutable');
function match_Mutable ($stack = array()) {
	$matchrule = "Mutable"; $result = $this->construct($matchrule, $matchrule, null);
	$_100 = NULL;
	do {
		$res_97 = $result;
		$pos_97 = $this->pos;
		$matcher = 'match_'.'VariableVector'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_100 = TRUE; break;
		}
		$result = $res_97;
		$this->pos = $pos_97;
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_100 = TRUE; break;
		}
		$result = $res_97;
		$this->pos = $pos_97;
		$_100 = FALSE; break;
	}
	while(0);
	if( $_100 === TRUE ) { return $this->finalise($result); }
	if( $_100 === FALSE) { return FALSE; }
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
	$_106 = NULL;
	do {
		$res_103 = $result;
		$pos_103 = $this->pos;
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
			$_106 = TRUE; break;
		}
		$result = $res_103;
		$this->pos = $pos_103;
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
			$_106 = TRUE; break;
		}
		$result = $res_103;
		$this->pos = $pos_103;
		$_106 = FALSE; break;
	}
	while(0);
	if( $_106 === TRUE ) { return $this->finalise($result); }
	if( $_106 === FALSE) { return FALSE; }
}


/* MultiplyOperator: "*" | "/" */
protected $match_MultiplyOperator_typestack = array('MultiplyOperator');
function match_MultiplyOperator ($stack = array()) {
	$matchrule = "MultiplyOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_111 = NULL;
	do {
		$res_108 = $result;
		$pos_108 = $this->pos;
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
			$_111 = TRUE; break;
		}
		$result = $res_108;
		$this->pos = $pos_108;
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
			$_111 = TRUE; break;
		}
		$result = $res_108;
		$this->pos = $pos_108;
		$_111 = FALSE; break;
	}
	while(0);
	if( $_111 === TRUE ) { return $this->finalise($result); }
	if( $_111 === FALSE) { return FALSE; }
}


/* AssignmentOperator: "=" */
protected $match_AssignmentOperator_typestack = array('AssignmentOperator');
function match_AssignmentOperator ($stack = array()) {
	$matchrule = "AssignmentOperator"; $result = $this->construct($matchrule, $matchrule, null);
	if (substr($this->string,$this->pos,1) == '=') {
		$this->pos += 1;
		$result["text"] .= '=';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* ComparisonOperator: "==" | "!=" | ">=" | "<=" | ">" | "<" */
protected $match_ComparisonOperator_typestack = array('ComparisonOperator');
function match_ComparisonOperator ($stack = array()) {
	$matchrule = "ComparisonOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_133 = NULL;
	do {
		$res_114 = $result;
		$pos_114 = $this->pos;
		if (( $subres = $this->literal( '==' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_133 = TRUE; break;
		}
		$result = $res_114;
		$this->pos = $pos_114;
		$_131 = NULL;
		do {
			$res_116 = $result;
			$pos_116 = $this->pos;
			if (( $subres = $this->literal( '!=' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_131 = TRUE; break;
			}
			$result = $res_116;
			$this->pos = $pos_116;
			$_129 = NULL;
			do {
				$res_118 = $result;
				$pos_118 = $this->pos;
				if (( $subres = $this->literal( '>=' ) ) !== FALSE) {
					$result["text"] .= $subres;
					$_129 = TRUE; break;
				}
				$result = $res_118;
				$this->pos = $pos_118;
				$_127 = NULL;
				do {
					$res_120 = $result;
					$pos_120 = $this->pos;
					if (( $subres = $this->literal( '<=' ) ) !== FALSE) {
						$result["text"] .= $subres;
						$_127 = TRUE; break;
					}
					$result = $res_120;
					$this->pos = $pos_120;
					$_125 = NULL;
					do {
						$res_122 = $result;
						$pos_122 = $this->pos;
						if (substr($this->string,$this->pos,1) == '>') {
							$this->pos += 1;
							$result["text"] .= '>';
							$_125 = TRUE; break;
						}
						$result = $res_122;
						$this->pos = $pos_122;
						if (substr($this->string,$this->pos,1) == '<') {
							$this->pos += 1;
							$result["text"] .= '<';
							$_125 = TRUE; break;
						}
						$result = $res_122;
						$this->pos = $pos_122;
						$_125 = FALSE; break;
					}
					while(0);
					if( $_125 === TRUE ) { $_127 = TRUE; break; }
					$result = $res_120;
					$this->pos = $pos_120;
					$_127 = FALSE; break;
				}
				while(0);
				if( $_127 === TRUE ) { $_129 = TRUE; break; }
				$result = $res_118;
				$this->pos = $pos_118;
				$_129 = FALSE; break;
			}
			while(0);
			if( $_129 === TRUE ) { $_131 = TRUE; break; }
			$result = $res_116;
			$this->pos = $pos_116;
			$_131 = FALSE; break;
		}
		while(0);
		if( $_131 === TRUE ) { $_133 = TRUE; break; }
		$result = $res_114;
		$this->pos = $pos_114;
		$_133 = FALSE; break;
	}
	while(0);
	if( $_133 === TRUE ) { return $this->finalise($result); }
	if( $_133 === FALSE) { return FALSE; }
}


/* UnaryOperator: "++" | "--" */
protected $match_UnaryOperator_typestack = array('UnaryOperator');
function match_UnaryOperator ($stack = array()) {
	$matchrule = "UnaryOperator"; $result = $this->construct($matchrule, $matchrule, null);
	$_138 = NULL;
	do {
		$res_135 = $result;
		$pos_135 = $this->pos;
		if (( $subres = $this->literal( '++' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_138 = TRUE; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		if (( $subres = $this->literal( '--' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$_138 = TRUE; break;
		}
		$result = $res_135;
		$this->pos = $pos_135;
		$_138 = FALSE; break;
	}
	while(0);
	if( $_138 === TRUE ) { return $this->finalise($result); }
	if( $_138 === FALSE) { return FALSE; }
}


/* Expression: skip:Assignment | skip:Comparison | skip:Addition */
protected $match_Expression_typestack = array('Expression');
function match_Expression ($stack = array()) {
	$matchrule = "Expression"; $result = $this->construct($matchrule, $matchrule, null);
	$_147 = NULL;
	do {
		$res_140 = $result;
		$pos_140 = $this->pos;
		$matcher = 'match_'.'Assignment'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_147 = TRUE; break;
		}
		$result = $res_140;
		$this->pos = $pos_140;
		$_145 = NULL;
		do {
			$res_142 = $result;
			$pos_142 = $this->pos;
			$matcher = 'match_'.'Comparison'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_145 = TRUE; break;
			}
			$result = $res_142;
			$this->pos = $pos_142;
			$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_145 = TRUE; break;
			}
			$result = $res_142;
			$this->pos = $pos_142;
			$_145 = FALSE; break;
		}
		while(0);
		if( $_145 === TRUE ) { $_147 = TRUE; break; }
		$result = $res_140;
		$this->pos = $pos_140;
		$_147 = FALSE; break;
	}
	while(0);
	if( $_147 === TRUE ) { return $this->finalise($result); }
	if( $_147 === FALSE) { return FALSE; }
}


/* Comparison: left:Addition > op:ComparisonOperator > right:Addition */
protected $match_Comparison_typestack = array('Comparison');
function match_Comparison ($stack = array()) {
	$matchrule = "Comparison"; $result = $this->construct($matchrule, $matchrule, null);
	$_154 = NULL;
	do {
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_154 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'ComparisonOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_154 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Addition'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_154 = FALSE; break; }
		$_154 = TRUE; break;
	}
	while(0);
	if( $_154 === TRUE ) { return $this->finalise($result); }
	if( $_154 === FALSE) { return FALSE; }
}


/* Assignment: left: Mutable > op:AssignmentOperator > right:Expression */
protected $match_Assignment_typestack = array('Assignment');
function match_Assignment ($stack = array()) {
	$matchrule = "Assignment"; $result = $this->construct($matchrule, $matchrule, null);
	$_161 = NULL;
	do {
		$matcher = 'match_'.'Mutable'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_161 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'AssignmentOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "op" );
		}
		else { $_161 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "right" );
		}
		else { $_161 = FALSE; break; }
		$_161 = TRUE; break;
	}
	while(0);
	if( $_161 === TRUE ) { return $this->finalise($result); }
	if( $_161 === FALSE) { return FALSE; }
}


/* Addition: operands:Multiplication ( > ops:AddOperator > operands:Multiplication)* */
protected $match_Addition_typestack = array('Addition');
function match_Addition ($stack = array()) {
	$matchrule = "Addition"; $result = $this->construct($matchrule, $matchrule, null);
	$_170 = NULL;
	do {
		$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_170 = FALSE; break; }
		while (true) {
			$res_169 = $result;
			$pos_169 = $this->pos;
			$_168 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'AddOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_168 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Multiplication'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_168 = FALSE; break; }
				$_168 = TRUE; break;
			}
			while(0);
			if( $_168 === FALSE) {
				$result = $res_169;
				$this->pos = $pos_169;
				unset( $res_169 );
				unset( $pos_169 );
				break;
			}
		}
		$_170 = TRUE; break;
	}
	while(0);
	if( $_170 === TRUE ) { return $this->finalise($result); }
	if( $_170 === FALSE) { return FALSE; }
}


/* Multiplication: operands:Operand ( > ops:MultiplyOperator > operands:Operand)* */
protected $match_Multiplication_typestack = array('Multiplication');
function match_Multiplication ($stack = array()) {
	$matchrule = "Multiplication"; $result = $this->construct($matchrule, $matchrule, null);
	$_179 = NULL;
	do {
		$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "operands" );
		}
		else { $_179 = FALSE; break; }
		while (true) {
			$res_178 = $result;
			$pos_178 = $this->pos;
			$_177 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'MultiplyOperator'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "ops" );
				}
				else { $_177 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'Operand'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "operands" );
				}
				else { $_177 = FALSE; break; }
				$_177 = TRUE; break;
			}
			while(0);
			if( $_177 === FALSE) {
				$result = $res_178;
				$this->pos = $pos_178;
				unset( $res_178 );
				unset( $pos_178 );
				break;
			}
		}
		$_179 = TRUE; break;
	}
	while(0);
	if( $_179 === TRUE ) { return $this->finalise($result); }
	if( $_179 === FALSE) { return FALSE; }
}


/* Chain: ObjectResolutionOperator ( core:MethodCall | core:PropertyGetter ) ( chain:Chain )? */
protected $match_Chain_typestack = array('Chain');
function match_Chain ($stack = array()) {
	$matchrule = "Chain"; $result = $this->construct($matchrule, $matchrule, null);
	$_192 = NULL;
	do {
		$matcher = 'match_'.'ObjectResolutionOperator'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_192 = FALSE; break; }
		$_187 = NULL;
		do {
			$_185 = NULL;
			do {
				$res_182 = $result;
				$pos_182 = $this->pos;
				$matcher = 'match_'.'MethodCall'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "core" );
					$_185 = TRUE; break;
				}
				$result = $res_182;
				$this->pos = $pos_182;
				$matcher = 'match_'.'PropertyGetter'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "core" );
					$_185 = TRUE; break;
				}
				$result = $res_182;
				$this->pos = $pos_182;
				$_185 = FALSE; break;
			}
			while(0);
			if( $_185 === FALSE) { $_187 = FALSE; break; }
			$_187 = TRUE; break;
		}
		while(0);
		if( $_187 === FALSE) { $_192 = FALSE; break; }
		$res_191 = $result;
		$pos_191 = $this->pos;
		$_190 = NULL;
		do {
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "chain" );
			}
			else { $_190 = FALSE; break; }
			$_190 = TRUE; break;
		}
		while(0);
		if( $_190 === FALSE) {
			$result = $res_191;
			$this->pos = $pos_191;
			unset( $res_191 );
			unset( $pos_191 );
		}
		$_192 = TRUE; break;
	}
	while(0);
	if( $_192 === TRUE ) { return $this->finalise($result); }
	if( $_192 === FALSE) { return FALSE; }
}


/* Operand: ( ( "(" > core:Expression > ")" ) | core:FunctionCall | core:DereferencableValue ) ( chain:Chain )? */
protected $match_Operand_typestack = array('Operand');
function match_Operand ($stack = array()) {
	$matchrule = "Operand"; $result = $this->construct($matchrule, $matchrule, null);
	$_214 = NULL;
	do {
		$_209 = NULL;
		do {
			$_207 = NULL;
			do {
				$res_194 = $result;
				$pos_194 = $this->pos;
				$_200 = NULL;
				do {
					if (substr($this->string,$this->pos,1) == '(') {
						$this->pos += 1;
						$result["text"] .= '(';
					}
					else { $_200 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
					}
					else { $_200 = FALSE; break; }
					if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
					if (substr($this->string,$this->pos,1) == ')') {
						$this->pos += 1;
						$result["text"] .= ')';
					}
					else { $_200 = FALSE; break; }
					$_200 = TRUE; break;
				}
				while(0);
				if( $_200 === TRUE ) { $_207 = TRUE; break; }
				$result = $res_194;
				$this->pos = $pos_194;
				$_205 = NULL;
				do {
					$res_202 = $result;
					$pos_202 = $this->pos;
					$matcher = 'match_'.'FunctionCall'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_205 = TRUE; break;
					}
					$result = $res_202;
					$this->pos = $pos_202;
					$matcher = 'match_'.'DereferencableValue'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "core" );
						$_205 = TRUE; break;
					}
					$result = $res_202;
					$this->pos = $pos_202;
					$_205 = FALSE; break;
				}
				while(0);
				if( $_205 === TRUE ) { $_207 = TRUE; break; }
				$result = $res_194;
				$this->pos = $pos_194;
				$_207 = FALSE; break;
			}
			while(0);
			if( $_207 === FALSE) { $_209 = FALSE; break; }
			$_209 = TRUE; break;
		}
		while(0);
		if( $_209 === FALSE) { $_214 = FALSE; break; }
		$res_213 = $result;
		$pos_213 = $this->pos;
		$_212 = NULL;
		do {
			$matcher = 'match_'.'Chain'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "chain" );
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
		}
		$_214 = TRUE; break;
	}
	while(0);
	if( $_214 === TRUE ) { return $this->finalise($result); }
	if( $_214 === FALSE) { return FALSE; }
}


/* MethodCall: method:VariableName "(" __ args:ArgumentList? __ ")" */
protected $match_MethodCall_typestack = array('MethodCall');
function match_MethodCall ($stack = array()) {
	$matchrule = "MethodCall"; $result = $this->construct($matchrule, $matchrule, null);
	$_222 = NULL;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "method" );
		}
		else { $_222 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_222 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_222 = FALSE; break; }
		$res_219 = $result;
		$pos_219 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_219;
			$this->pos = $pos_219;
			unset( $res_219 );
			unset( $pos_219 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_222 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_222 = FALSE; break; }
		$_222 = TRUE; break;
	}
	while(0);
	if( $_222 === TRUE ) { return $this->finalise($result); }
	if( $_222 === FALSE) { return FALSE; }
}


/* FunctionCall: function:VariableName "(" __ args:ArgumentList? __ ")" */
protected $match_FunctionCall_typestack = array('FunctionCall');
function match_FunctionCall ($stack = array()) {
	$matchrule = "FunctionCall"; $result = $this->construct($matchrule, $matchrule, null);
	$_230 = NULL;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_230 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_230 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_230 = FALSE; break; }
		$res_227 = $result;
		$pos_227 = $this->pos;
		$matcher = 'match_'.'ArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_227;
			$this->pos = $pos_227;
			unset( $res_227 );
			unset( $pos_227 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_230 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_230 = FALSE; break; }
		$_230 = TRUE; break;
	}
	while(0);
	if( $_230 === TRUE ) { return $this->finalise($result); }
	if( $_230 === FALSE) { return FALSE; }
}


/* ArgumentList: args:Expression ( __ "," __ args:Expression )* */
protected $match_ArgumentList_typestack = array('ArgumentList');
function match_ArgumentList ($stack = array()) {
	$matchrule = "ArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_239 = NULL;
	do {
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else { $_239 = FALSE; break; }
		while (true) {
			$res_238 = $result;
			$pos_238 = $this->pos;
			$_237 = NULL;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_237 = FALSE; break; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_237 = FALSE; break; }
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_237 = FALSE; break; }
				$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "args" );
				}
				else { $_237 = FALSE; break; }
				$_237 = TRUE; break;
			}
			while(0);
			if( $_237 === FALSE) {
				$result = $res_238;
				$this->pos = $pos_238;
				unset( $res_238 );
				unset( $pos_238 );
				break;
			}
		}
		$_239 = TRUE; break;
	}
	while(0);
	if( $_239 === TRUE ) { return $this->finalise($result); }
	if( $_239 === FALSE) { return FALSE; }
}


/* FunctionDefinitionArgumentList: skip:VariableName ( > "," > skip:VariableName )* */
protected $match_FunctionDefinitionArgumentList_typestack = array('FunctionDefinitionArgumentList');
function match_FunctionDefinitionArgumentList ($stack = array()) {
	$matchrule = "FunctionDefinitionArgumentList"; $result = $this->construct($matchrule, $matchrule, null);
	$_248 = NULL;
	do {
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
		}
		else { $_248 = FALSE; break; }
		while (true) {
			$res_247 = $result;
			$pos_247 = $this->pos;
			$_246 = NULL;
			do {
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_246 = FALSE; break; }
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
				}
				else { $_246 = FALSE; break; }
				$_246 = TRUE; break;
			}
			while(0);
			if( $_246 === FALSE) {
				$result = $res_247;
				$this->pos = $pos_247;
				unset( $res_247 );
				unset( $pos_247 );
				break;
			}
		}
		$_248 = TRUE; break;
	}
	while(0);
	if( $_248 === TRUE ) { return $this->finalise($result); }
	if( $_248 === FALSE) { return FALSE; }
}


/* FunctionDefinition: "function" [ function:VariableName __ "(" > args:FunctionDefinitionArgumentList? > ")" __ body:Block */
protected $match_FunctionDefinition_typestack = array('FunctionDefinition');
function match_FunctionDefinition ($stack = array()) {
	$matchrule = "FunctionDefinition"; $result = $this->construct($matchrule, $matchrule, null);
	$_261 = NULL;
	do {
		if (( $subres = $this->literal( 'function' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_261 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_261 = FALSE; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "function" );
		}
		else { $_261 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_261 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_261 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$res_256 = $result;
		$pos_256 = $this->pos;
		$matcher = 'match_'.'FunctionDefinitionArgumentList'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "args" );
		}
		else {
			$result = $res_256;
			$this->pos = $pos_256;
			unset( $res_256 );
			unset( $pos_256 );
		}
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_261 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_261 = FALSE; break; }
		$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "body" );
		}
		else { $_261 = FALSE; break; }
		$_261 = TRUE; break;
	}
	while(0);
	if( $_261 === TRUE ) { return $this->finalise($result); }
	if( $_261 === FALSE) { return FALSE; }
}


/* IfStatement: "if" __ "(" __ left:Expression __ ")" __ ( right:Block ) > */
protected $match_IfStatement_typestack = array('IfStatement');
function match_IfStatement ($stack = array()) {
	$matchrule = "IfStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_275 = NULL;
	do {
		if (( $subres = $this->literal( 'if' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_275 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_275 = FALSE; break; }
		$_272 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_272 = FALSE; break; }
			$_272 = TRUE; break;
		}
		while(0);
		if( $_272 === FALSE) { $_275 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_275 = TRUE; break;
	}
	while(0);
	if( $_275 === TRUE ) { return $this->finalise($result); }
	if( $_275 === FALSE) { return FALSE; }
}


/* WhileStatement: "while" __ "(" __ left:Expression __ ")" __ ( right:Block ) > */
protected $match_WhileStatement_typestack = array('WhileStatement');
function match_WhileStatement ($stack = array()) {
	$matchrule = "WhileStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_289 = NULL;
	do {
		if (( $subres = $this->literal( 'while' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_289 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_289 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_289 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_289 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_289 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_289 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_289 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_289 = FALSE; break; }
		$_286 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_286 = FALSE; break; }
			$_286 = TRUE; break;
		}
		while(0);
		if( $_286 === FALSE) { $_289 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		$_289 = TRUE; break;
	}
	while(0);
	if( $_289 === TRUE ) { return $this->finalise($result); }
	if( $_289 === FALSE) { return FALSE; }
}


/* ForeachStatement: "foreach" __ "(" __ left:Expression __ "as" __ item:VariableName __ ")" __ ( right:Block ) */
protected $match_ForeachStatement_typestack = array('ForeachStatement');
function match_ForeachStatement ($stack = array()) {
	$matchrule = "ForeachStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_306 = NULL;
	do {
		if (( $subres = $this->literal( 'foreach' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_306 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '(') {
			$this->pos += 1;
			$result["text"] .= '(';
		}
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "left" );
		}
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_306 = FALSE; break; }
		if (( $subres = $this->literal( 'as' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'VariableName'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "item" );
		}
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_306 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ')') {
			$this->pos += 1;
			$result["text"] .= ')';
		}
		else { $_306 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_306 = FALSE; break; }
		$_304 = NULL;
		do {
			$matcher = 'match_'.'Block'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "right" );
			}
			else { $_304 = FALSE; break; }
			$_304 = TRUE; break;
		}
		while(0);
		if( $_304 === FALSE) { $_306 = FALSE; break; }
		$_306 = TRUE; break;
	}
	while(0);
	if( $_306 === TRUE ) { return $this->finalise($result); }
	if( $_306 === FALSE) { return FALSE; }
}


/* BlockStatements: skip:IfStatement | skip:WhileStatement | skip:ForeachStatement | skip:FunctionDefinition */
protected $match_BlockStatements_typestack = array('BlockStatements');
function match_BlockStatements ($stack = array()) {
	$matchrule = "BlockStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_319 = NULL;
	do {
		$res_308 = $result;
		$pos_308 = $this->pos;
		$matcher = 'match_'.'IfStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_319 = TRUE; break;
		}
		$result = $res_308;
		$this->pos = $pos_308;
		$_317 = NULL;
		do {
			$res_310 = $result;
			$pos_310 = $this->pos;
			$matcher = 'match_'.'WhileStatement'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_317 = TRUE; break;
			}
			$result = $res_310;
			$this->pos = $pos_310;
			$_315 = NULL;
			do {
				$res_312 = $result;
				$pos_312 = $this->pos;
				$matcher = 'match_'.'ForeachStatement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_315 = TRUE; break;
				}
				$result = $res_312;
				$this->pos = $pos_312;
				$matcher = 'match_'.'FunctionDefinition'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "skip" );
					$_315 = TRUE; break;
				}
				$result = $res_312;
				$this->pos = $pos_312;
				$_315 = FALSE; break;
			}
			while(0);
			if( $_315 === TRUE ) { $_317 = TRUE; break; }
			$result = $res_310;
			$this->pos = $pos_310;
			$_317 = FALSE; break;
		}
		while(0);
		if( $_317 === TRUE ) { $_319 = TRUE; break; }
		$result = $res_308;
		$this->pos = $pos_308;
		$_319 = FALSE; break;
	}
	while(0);
	if( $_319 === TRUE ) { return $this->finalise($result); }
	if( $_319 === FALSE) { return FALSE; }
}


/* CommandStatements: skip:EchoStatement | skip:ReturnStatement */
protected $match_CommandStatements_typestack = array('CommandStatements');
function match_CommandStatements ($stack = array()) {
	$matchrule = "CommandStatements"; $result = $this->construct($matchrule, $matchrule, null);
	$_324 = NULL;
	do {
		$res_321 = $result;
		$pos_321 = $this->pos;
		$matcher = 'match_'.'EchoStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_324 = TRUE; break;
		}
		$result = $res_321;
		$this->pos = $pos_321;
		$matcher = 'match_'.'ReturnStatement'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_324 = TRUE; break;
		}
		$result = $res_321;
		$this->pos = $pos_321;
		$_324 = FALSE; break;
	}
	while(0);
	if( $_324 === TRUE ) { return $this->finalise($result); }
	if( $_324 === FALSE) { return FALSE; }
}


/* EchoStatement: "echo" [ subject:Expression */
protected $match_EchoStatement_typestack = array('EchoStatement');
function match_EchoStatement ($stack = array()) {
	$matchrule = "EchoStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_329 = NULL;
	do {
		if (( $subres = $this->literal( 'echo' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_329 = FALSE; break; }
		if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_329 = FALSE; break; }
		$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "subject" );
		}
		else { $_329 = FALSE; break; }
		$_329 = TRUE; break;
	}
	while(0);
	if( $_329 === TRUE ) { return $this->finalise($result); }
	if( $_329 === FALSE) { return FALSE; }
}


/* ReturnStatement: "return" ( [ subject:Expression )? */
protected $match_ReturnStatement_typestack = array('ReturnStatement');
function match_ReturnStatement ($stack = array()) {
	$matchrule = "ReturnStatement"; $result = $this->construct($matchrule, $matchrule, null);
	$_336 = NULL;
	do {
		if (( $subres = $this->literal( 'return' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_336 = FALSE; break; }
		$res_335 = $result;
		$pos_335 = $this->pos;
		$_334 = NULL;
		do {
			if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
			else { $_334 = FALSE; break; }
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "subject" );
			}
			else { $_334 = FALSE; break; }
			$_334 = TRUE; break;
		}
		while(0);
		if( $_334 === FALSE) {
			$result = $res_335;
			$this->pos = $pos_335;
			unset( $res_335 );
			unset( $pos_335 );
		}
		$_336 = TRUE; break;
	}
	while(0);
	if( $_336 === TRUE ) { return $this->finalise($result); }
	if( $_336 === FALSE) { return FALSE; }
}


/* Statement: skip:BlockStatements | skip:CommandStatements | skip:Expression */
protected $match_Statement_typestack = array('Statement');
function match_Statement ($stack = array()) {
	$matchrule = "Statement"; $result = $this->construct($matchrule, $matchrule, null);
	$_345 = NULL;
	do {
		$res_338 = $result;
		$pos_338 = $this->pos;
		$matcher = 'match_'.'BlockStatements'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres, "skip" );
			$_345 = TRUE; break;
		}
		$result = $res_338;
		$this->pos = $pos_338;
		$_343 = NULL;
		do {
			$res_340 = $result;
			$pos_340 = $this->pos;
			$matcher = 'match_'.'CommandStatements'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_343 = TRUE; break;
			}
			$result = $res_340;
			$this->pos = $pos_340;
			$matcher = 'match_'.'Expression'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
				$_343 = TRUE; break;
			}
			$result = $res_340;
			$this->pos = $pos_340;
			$_343 = FALSE; break;
		}
		while(0);
		if( $_343 === TRUE ) { $_345 = TRUE; break; }
		$result = $res_338;
		$this->pos = $pos_338;
		$_345 = FALSE; break;
	}
	while(0);
	if( $_345 === TRUE ) { return $this->finalise($result); }
	if( $_345 === FALSE) { return FALSE; }
}


/* Block: "{" __ ( skip:Program )? __ "}" */
protected $match_Block_typestack = array('Block');
function match_Block ($stack = array()) {
	$matchrule = "Block"; $result = $this->construct($matchrule, $matchrule, null);
	$_354 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '{') {
			$this->pos += 1;
			$result["text"] .= '{';
		}
		else { $_354 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_354 = FALSE; break; }
		$res_351 = $result;
		$pos_351 = $this->pos;
		$_350 = NULL;
		do {
			$matcher = 'match_'.'Program'; $key = $matcher; $pos = $this->pos;
			$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
			if ($subres !== FALSE) {
				$this->store( $result, $subres, "skip" );
			}
			else { $_350 = FALSE; break; }
			$_350 = TRUE; break;
		}
		while(0);
		if( $_350 === FALSE) {
			$result = $res_351;
			$this->pos = $pos_351;
			unset( $res_351 );
			unset( $pos_351 );
		}
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_354 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '}') {
			$this->pos += 1;
			$result["text"] .= '}';
		}
		else { $_354 = FALSE; break; }
		$_354 = TRUE; break;
	}
	while(0);
	if( $_354 === TRUE ) { return $this->finalise($result); }
	if( $_354 === FALSE) { return FALSE; }
}


/* __: /([\s\n]*)/ */
protected $match____typestack = array('__');
function match___ ($stack = array()) {
	$matchrule = "__"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/([\s\n]*)/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* NL: /(\/\/[^\n]*)?\n/ */
protected $match_NL_typestack = array('NL');
function match_NL ($stack = array()) {
	$matchrule = "NL"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/(\/\/[^\n]*)?\n/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* SEP: NL | ";" */
protected $match_SEP_typestack = array('SEP');
function match_SEP ($stack = array()) {
	$matchrule = "SEP"; $result = $this->construct($matchrule, $matchrule, null);
	$_361 = NULL;
	do {
		$res_358 = $result;
		$pos_358 = $this->pos;
		$matcher = 'match_'.'NL'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) {
			$this->store( $result, $subres );
			$_361 = TRUE; break;
		}
		$result = $res_358;
		$this->pos = $pos_358;
		if (substr($this->string,$this->pos,1) == ';') {
			$this->pos += 1;
			$result["text"] .= ';';
			$_361 = TRUE; break;
		}
		$result = $res_358;
		$this->pos = $pos_358;
		$_361 = FALSE; break;
	}
	while(0);
	if( $_361 === TRUE ) { return $this->finalise($result); }
	if( $_361 === FALSE) { return FALSE; }
}


/* Program: ( __ Statement? > SEP )+ __ */
protected $match_Program_typestack = array('Program');
function match_Program ($stack = array()) {
	$matchrule = "Program"; $result = $this->construct($matchrule, $matchrule, null);
	$_370 = NULL;
	do {
		$count = 0;
		while (true) {
			$res_368 = $result;
			$pos_368 = $this->pos;
			$_367 = NULL;
			do {
				$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_367 = FALSE; break; }
				$res_364 = $result;
				$pos_364 = $this->pos;
				$matcher = 'match_'.'Statement'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else {
					$result = $res_364;
					$this->pos = $pos_364;
					unset( $res_364 );
					unset( $pos_364 );
				}
				if (( $subres = $this->whitespace(  ) ) !== FALSE) { $result["text"] .= $subres; }
				$matcher = 'match_'.'SEP'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) { $this->store( $result, $subres ); }
				else { $_367 = FALSE; break; }
				$_367 = TRUE; break;
			}
			while(0);
			if( $_367 === FALSE) {
				$result = $res_368;
				$this->pos = $pos_368;
				unset( $res_368 );
				unset( $pos_368 );
				break;
			}
			$count++;
		}
		if ($count >= 1) {  }
		else { $_370 = FALSE; break; }
		$matcher = 'match_'.'__'; $key = $matcher; $pos = $this->pos;
		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
		if ($subres !== FALSE) { $this->store( $result, $subres ); }
		else { $_370 = FALSE; break; }
		$_370 = TRUE; break;
	}
	while(0);
	if( $_370 === TRUE ) { return $this->finalise($result); }
	if( $_370 === FALSE) { return FALSE; }
}




}
