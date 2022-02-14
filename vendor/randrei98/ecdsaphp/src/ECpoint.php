<?php
declare(strict_types=1);

namespace ECDSA;


Class ECpoint {
	private $x;
	private $y;
	private $z;

	function __construct($x, $y, $z){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	public function x() {
		return $this->x;
	}

	public function y() {
		return $this->y;
	}

	public function z() {
		return $this->z;
	}
}
?>