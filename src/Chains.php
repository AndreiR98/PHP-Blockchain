<?php
/**
 * Class Chains
 *
 * @package php-blockchain
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
*/

declare(strict_types=1);

namespace Blockchain;

use Chain\Chain;

class Chains implements Chain{
    private $files = [];

    public function __construct(){
        $path = 'E:\xampp\htdocs\PHP-Blockchain\Chains';


        $files = glob($path."/*.CHAIN");

        foreach ($files as $file){
            $file = explode("/", $file);
            $file = explode(".", $file[1]);
            array_push($this->files, $file[0]);
        }
    }

    public function getChains(){
        return var_dump($this->files);
    }
}