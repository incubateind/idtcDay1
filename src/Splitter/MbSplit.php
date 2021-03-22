<?php

namespace Keboola\DockerDemo\Splitter;

class MbSplit
{

    /**
     * @param $string
     * @param int $maxLength
     * @return array
     * @throws Exception
     */
    public static function split($string, $maxLength = 255)
    {
        if ($maxLength <= 0) {
            throw new Exception("maxLength must be greater than 0");
        }
        $ret = array();
        $len = mb_strlen($string, "UTF-8");
        for ($i = 0; $i < $len; $i += $maxLength) {
            $ret[] = mb_substr($string, $i, $maxLength, "UTF-8");
        }

        return $ret;
    }
}
