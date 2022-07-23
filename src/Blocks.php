<?php
/**
 * Class EcDSA
 *
 * @package ecdsa/ecdsaphp
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
*/

declare(strict_types=1);

namespace Blockchain;

use Block\Block;
use Chain\Chain;

class Blocks implements Block{
	public function __construct(){}

	/**
	 *Create genesis block
	 * 
	 * @param object Chain
	 * 
	 * @return object Block 
	 */
	public function genesis(Chain $chain): Block{}

	/**
	 * Hash the current block
	 * 
	 * @return string
	 */
	public static function hashing(): string{}

	/**
	 * Return the last block from chain
	 * 
	 * @param object chain
	 * 
	 * @return object block
	 */ 
	public function last_block(Chain $chain): Block{}

	/**
	 * Create a new block
	 * 
	 * @param object chain
	 * 
	 * @param string previous_hash
	 * 
	 * @return void
	 */ 
	public function new_block(Chain $chain, string $previous_hash): void{}



	public function test_block(Chain $chain){
		return $chain->getChains();
	}
}