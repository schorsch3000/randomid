<?php
namespace randomId;

class RandomId
{
    public static function createBlob($bytes, $filename, $shuffle = true)
    {
        $bitLength = 8 * $bytes;
        $length = pow(2, $bitLength);
        $a = range(0, $length - 1);
        if ($shuffle) {
            shuffle($a);
        }

        $fp = fopen($filename, 'w');

        foreach ($a as $r) {
            $number = [];
            while ($r) {
                $firstByte = 0xff & $r;
                $number[] = $firstByte;
                $r -= $firstByte;
                $r >>= 8;
            }
            while (count($number) < $bytes) {
                $number[] = 0;
            }
            $number = array_reverse($number);
            foreach ($number as $numberByte) {
                fwrite($fp, chr($numberByte));
            }

        }
    }

    public static function readRandomId($id, $filename, $bytes)
    {
        $fp = fopen($filename, 'r');
        fseek($fp, $id * $bytes);
        $pos=ftell($fp);
        if($pos+$bytes > filesize($filename)){
            throw new \ErrorException("Requested id ($id) is to large for blob ($filename)");
        }
        $rid = 0;
        for ($i = 0; $i < $bytes; $i++) {
            $rid <<= 8;
            $rid += ord(fread($fp, 1));
        }
        return $rid;
    }
}