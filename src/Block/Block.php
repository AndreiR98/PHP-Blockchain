<?php
/**
 * Class Block
 *
 * @package php-blockchain
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
 * 
 * Structure of a block from the chain
 * 
 * 
 */

declare(strict_types=1);

namespace Block;

use Chain\Chain;


Interface Block{
    /**
     * Create the first block on the chain
     * 
     * @param object Chain
     * 
     * @return object block
     */
	public function genesis(Chain $chain);

    /**
     * Hasing the curent block
     * 
     * @return string
     */
    public static function hashing();

    /**
     * Return the last block from the chain
     * 
     * @param object Chain
     * 
     * @return object block
     */
    public function last_block(Chain $chain);

    /**
     * Create a new block on the chain
     * 
     * @param object Chain
     * 
     * @param string previous_hash
     * 
     * @return object Block
     */
    public function new_block(Chain $chain, string $previous_hash);

    public function test_block(Chain $chain);
}