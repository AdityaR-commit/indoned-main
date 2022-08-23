<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 27-04-2021
 * Time: 19:48
 */

namespace app\libraries\Arkatama\crypto;

defined('BASEPATH') OR exit('No direct script access allowed');


class Bijective
{
    public $dictionary = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    const BASE_LENGTH = 8;

    public function __construct()
    {
        $this->dictionary = str_split($this->dictionary);
    }

    public function encode($i, $length = self::BASE_LENGTH)
    {
        $i = substr($i, 0, $length);
        if ($i == 0)
            return $this->dictionary[0];

        $result = '';
        $base   = count($this->dictionary);

        while ($i > 0) {
            $result[] = $this->dictionary[($i % $base)];
            $i        = floor($i / $base);
        }

        $result = array_reverse($result);

        return join("", $result);
    }

    public function decode($input)
    {
        $i    = 0;
        $base = count($this->dictionary);

        $input = str_split($input);

        foreach ($input as $char) {
            $pos = array_search($char, $this->dictionary);

            $i = $i * $base + $pos;
        }

        return $i;
    }
}