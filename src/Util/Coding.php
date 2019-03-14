<?php

namespace SloopMini\Util;


class Coding{
    static $mapbit = array(
        5,  2,  4,  1,
        29,  3, 28,  0,
        13,  7, 11,  6,
        8, 12,  9, 10,

        15, 14, 23, 16,
        21, 25, 24, 18,
        20, 17, 27, 22,
        26, 19, 30, 31,
    );


    public static function intEncode($id, array $mapbit=array()) {

        if (empty($mapbit)) {
            $mapbit = self::$mapbit;
        }
        $r = 0;
        $sign = 0x1 << 30;
        $id |= $sign;
        for ($x = 0; $x < 31; $x++) {
            $v = ($id >> $x) & 0x1;
            $r |= ($v << $mapbit[$x]);
        }
        return $r;
    }

    public static function intDecode($id, array $mapbit=array()) {
        if (empty($mapbit)) {
            $mapbit = self::$mapbit;
        }

        $r = 0;
        for ($x = 0; $x < 30; $x++) {
            $v = ($id >> $mapbit[$x]) & 0x1;
            $r |= ($v << $x);
        }
        return $r;
    }



    static function  tiriEncode($str , $factor = 0){
        $len = strlen($str);
        if(!$len){
            return;
        }
        if($factor  === 0){
            $factor = mt_rand(1, min(255 , ceil($len / 3)));
        }
        $c = $factor % 8;

        $slice = str_split($str ,$factor);
        for($i=0;$i < count($slice);$i++){
            for($j=0;$j< strlen($slice[$i]) ;$j ++){
                $slice[$i][$j] = chr(ord($slice[$i][$j]) + $c + $i);
            }
        }
        $ret = pack('C' , $factor).implode('' , $slice);
        return self::base64URLEncode($ret);
    }

    static function tiriDecode($str){
        if($str == ''){
            return;
        }
        $str = self::base64URLDecode($str);
        $factor =  ord(substr($str , 0 ,1));
        $c = $factor % 8;
        $entity = substr($str , 1);
        $slice = str_split($entity , $factor);
        if(!$slice){
            return false;
        }
        for($i=0;$i < count($slice); $i++){
            for($j =0 ; $j < strlen($slice[$i]); $j++){
                $slice[$i][$j] = chr(ord($slice[$i][$j]) - $c - $i );
            }
        }
        return implode($slice);
    }

    static function base64URLEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    static function base64URLDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    static function stringXor($str){
        for ($i = 0; $i < strlen($str); ++$i) {
            $str[$i] = chr(ord($str[$i]) ^ 0x7F);
        }
        return $str;
    }
}
