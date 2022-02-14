<?php
/**
 * Class Algorithms
 *
 * @package ecdsa/ecdsaphp
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
 */
namespace ECDSA;

Class Algorithms {

	public $name;

	public $hash;

	public function __construct($name, $hash){
		$this->name = $name;
		$this->hash = $hash;
	}

	public static function name() {
		return $this->name;
	}
	
	public static function ES256(){
		return new Algorithms('ES256', 'sha256');
	}

	public static function ES3_256(){
		return new Algorithms('ES3_256', 'sha3-256');
	}
}
?>