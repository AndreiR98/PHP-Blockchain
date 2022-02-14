<?php
/**
 * Class Point Jacobi
 *
 * @package ecdsa/ecdsaphp
 * @author  Rotaru Andrei <rotaru.andrei98@gmail.com>
 */


namespace ECDSA;

use ECDSA\ECpoint;
use ECDSA\Curves;

Class PointJacobi {
    private $x;
    private $y;
    private $z;
    private $curve;

    private $a;
    private $p;

    function __construct($ECpoint, $curve){
        $this->x = $ECpoint->x();
        $this->y = $ECpoint->y();
        $this->z = $ECpoint->z();
        $this->curve = $curve;

        $this->a = $curve->a();
        $this->p = $curve->p();
    }

    public function xs() {
        //Scale X  after Jacobi transform
        if ($this->z == 1){
            return $this->x;
        }

        $p = $this->p;
        $z = gmp_invert($this->z, $p);
        return ($this->x * $z ** 2) % $p;
    }


    public function ys(){
        //Scale Y after Jacobi transform
        if ($this->z == 1){
            return $this->y;
        }

        $p = $this->p;
        $z = gmp_invert($this->z, $p);
        return ($this->y * $z ** 3) % $p;
    }

    public function x(){
        //Return x coord
        return $this->x;
    }

    public function y() {
        //Return y coord
        return $this->y;
    }

    public function z(){
        return $this->z;
    }

    public function curve(){
        return $this->curve;
    }

    public function to_affine(){
        //Return set of afine coords
        return ['x'=>$this->xs(), 'y'=>$this->ys(), 'z'=>$this->z()];
    }

    function _coords(){
        return [$this->x(), $this->y(), $this->z()];
    }

    public function scale($coords){
        [$X, $Y, $Z] = $coords;

        $p = $this->p;

        $Z_inv = gmp_invert($Z, $p);
        $ZZ_inv = ($Z_inv * $Z_inv) % $p;

        $X = ($X * $ZZ_inv) % $p;
        $Y = ($Y * $ZZ_inv * $ZZ_inv) % $p;

        return [$X, $Y, 1];
    }

    public function _double_with_z1($X1, $Y1){
        //Add a point to itself when Z=1
        $p = $this->p;
        $a = $this->a;

        $XX = ($X1 * $X1) % $p;
        $YY = ($Y1 * $Y1) % $p;

        if ($YY == 0){
            [0, 0, 1];
        }

        $YYYY = ($YY * $YY) % $p;
        $S = 2 * (($X1 + $YY) ** 2 - $XX - $YYYY) % $p;
        $M = 3 * $XX + $a;
        $T = ($M * $M - 2 * $S) % $p;
        $Y3 = ($M * ($S - $T) - 8 * $YYYY) % $p;
        $Z3 = 2 * $Y1 % $p;

        return [$T, $Y3, $Z3];
    }

    public function _double($X1, $Y1, $Z1){
        //Add a point to itself(double it) Jacobi coords.
        $p = $this->p;
        $a = $this->a;

        if ($Z1 == 1){
            return self::_double_with_z1($X1, $Y1);
        }

        if (($Y1 == 0)||($Z1 == 0)) {
            return [0, 0, 1];
        }

        $XX = ($X1 * $X1) % $p;
        $YY = ($Y1 * $Y1) % $p;

        if ($YY == 0){
            return [0, 0, 1];
        }

        $YYYY = ($YY * $YY) % $p;
        $ZZ = ($Z1 * $Z1) % $p;

        $S = 2 * (($X1 + $YY) ** 2 - $XX - $YYYY) % $p;
        $M = (3 * $XX + $a * $ZZ * $ZZ) % $p;
        $T = ($M * $M - 2 * $S) % $p;
        $Y3 = ($M * ($S- $T) - 8 * $YYYY) % $p;
        $Z3 = (($Y1 + $Z1) ** 2 - $YY - $ZZ) % $p;

        return [$T, $Y3, $Z3];
    }

    public function double(){
        //Double the point
        $X1 = self::x();
        $Y1 = self::y();
        $Z1 = self::z();

        $p = $this->p;
        $a = $this->a;

        [$X3, $Y3, $Z3] = self::_double($X1, $Y1, $Z1);

        return new PointJacobi(new ECpoint($X3, $Y3, $Z3), $this->curve);
    }

    public function _add_with_z_1($X1, $Y1, $X2, $Y2){
        //Add 2 points when both Z1 = 1 and Z2 = 1
        $p = $this->p;
        $a = $this->a;

        $H = $X2 - $X1;
        $HH = $H * $H;
        $I = (4 * $HH) % $p;
        $J = $H * $I;
        $r = 2 * ($Y2 - $Y1);

        if (($H == 0)&&($r == 0)){
            return self::_double_with_z1($X1, $Y1);
        }

        $V = $X1 * $I;
        $X3 = ($r ** 2 - $J - 2 * $V) % $p;
        $Y3 = ($r * ($V - $X3) - 2 * $Y1 * $J) % $p;
        $Z3 = (2 * $H) % $p;

        return [$X3, $Y3, $Z3];
    }

    public function _add_with_z_eq($X1, $Y1, $Z1, $X2, $Y2){
        //Add 2 points when Z1 = Z2
        $p = $this->p;
        $a = $this->a;

        $A = ($X2 - $X1) ** 2 % $p;
        $B = ($X1 * $A) % $p;
        $C = $X2 * $A;
        $D = ($Y2 - $Y1) ** 2 % $p;

        if (($A == 0)&&($D == 0)){
            return self::_double($X1, $Y1, $Z1);
        }

        $X3 = ($D - $B - $C) % $p;
        $Y3 = (($Y2 - $Y1) * ($B - $X3) -  $Y1 * ($C -  $B)) % $p;
        $Z3 = ($Z1 * ($X2 - $X1)) % $p;

        return [$X3, $Y3, $Z3];
    }

    public function _add_with_z2_1($X1, $Y1, $Z1, $X2, $Y2){
        // Add 2 points when Z2 = 1
        $p = $this->p;

        $Z1Z1 = ($Z1 * $Z1) % $p;
        $U2 = ($X2 * $Z1Z1) % $p;
        $S2 = ($Y2 * $Z1 * $Z1Z1) % $p;
        $H = ($U2 - $X1) % $p;
        $HH = ($H * $H) % $p;

        $I = (4 * $HH) % $p;
        $J = $H * $I;

        $r = 2 * ($S2 - $Y1) % $p;

        if (($r == 0)&&($H == 0)){
            return self::_double_with_z1($X2, $Y1);
        }

        $V = $X1 * $I;
        $X3 = ($r * $r - $J - 2 * $V) % $p;
        $Y3 = ($r * ($V - $X3) - 2 * $Y1 * $J) % $p;
        $Z3 = (($Z1 + $H) ** 2 - $Z1Z1 - $HH) % $p;

        return [$X3, $Y3, $Z3];
    }

    public function _add_with_z_ne($X1, $Y1, $Z1, $X2, $Y2, $Z2){
        //Add 2 points in Jacobi coords
        $p = $this->p;

        $Z1Z1 = ($Z1 * $Z1) % $p;
        $Z2Z2 = ($Z2 * $Z2) % $p;
        $U1 = ($X1 * $Z2Z2) % $p;
        $U2 = ($X2 * $Z1Z1) % $p;
        $S1 = ($Y1 * $Z2 * $Z2Z2) % $p;
        $S2 = ($Y2 * $Z1 * $Z1Z1) % $p;
        $H = $U2 - $U1;
        $I = (4 * ($H * $H)) % $p;
        $J = ($H * $I) % $p;

        $r = 2 * ($S2 - $S1) % $p;

        if (($H == 0)&&($r == 0)){
            return self::_double($X1, $Y1, $Z1);
        } 
        $V = $U1 * $I;
        $X3 = ($r * $r - $J - 2 * $V) % $p;
        $Y3 = ($r * ($V - $X3) - 2 * $S1 * $J) % $p;
        $Z3 = (($Z1 + $Z2) ** 2 - $Z1Z1 - $Z2Z2) % $p;

        return [$X3, $Y3, $Z3];

    }

    public function _add($X1, $Y1, $Z1, $X2, $Y2, $Z2) {
        if (($Y1 == 0)||($Z1 == 0)){
            return [$X2, $Y2, $Z2];
        }

        if (($Y2 == 0)||($Z2 == 0)){
            return [$X1, $Y1, $Z1];
        }

        if ($Z1 == $Z2) {
            if($Z1 == 1) {
                return self::_add_with_z_1($X1, $Y1, $X2, $Y2);
            }
            return self::_add_with_z_eq($X1, $Y1, $Z1, $X2, $Y2);
        }

        if ($Z1 == 1){
            return self::_add_with_z2_1($X2, $Y2, $Z2, $X1, $Y1);
        }

        if ($Z2 == 1){
            return self::_add_with_z2_1($X1, $Y1, $Z1, $X2, $Y2);
        }

        return self::_add_with_z_ne($X1, $Y1, $Z1, $X2, $Y2, $Z2);
    }

    public function add($p2){
        //Add 2 points in Jacobi coords, faster method always

        [$X1, $Y1, $Z1] = [$this->xs(), $this->ys(), 1];
        [$X2, $Y2, $Z2] = [$p2->x(), $p2->y(), $p2->z()];

        [$X3, $Y3, $Z3] = self::_add($X1, $Y1, $Z1, $X2, $Y2, $Z2);

        return new PointJacobi(new ECpoint($X3, $Y3, $Z3), $this->curve);
        
    }

    public static function _naf($mult){
        $ret = [];

        while ($mult > 0){
            if ($mult % 2 > 0) {
                $nb = $mult % 4;
                if ($nb >= 2){
                    $nb -= 4;
                }
                array_push($ret, $nb);
                $mult -= $nb;
            }else{
                array_push($ret, 0);
            }
            $mult = gmp_div($mult, 2);
        }
        return $ret;
    }

    public function _mul($mult) {

        [$X2, $Y2, $Z2] = $this->scale($this->_coords());
        [$X3, $Y3, $Z3] = [0, 0, 1];

        foreach(array_reverse($this->_naf($mult)) as $i){
            [$X3, $Y3, $Z3] = self::_double($X3, $Y3, $Z3);

            if ($i < 0){
                [$X3, $Y3, $Z3] = self::_add($X3, $Y3, $Z3, $X2, -$Y2, 1);
            }elseif($i > 0){
                [$X3, $Y3, $Z3] = self::_add($X3, $Y3, $Z3, $X2, $Y2, 1);
            }
        }

        return new PointJacobi(new ECpoint($X3, $Y3, $Z3), $this->curve);
        
    }
}