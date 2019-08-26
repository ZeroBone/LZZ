<?php


namespace Lzz\Utils;


class UrlUtils {

    const SHORTCODE_BASE_CHARS = '123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
    const SHORTCODE_BASE = 50;

    private static function rotateString($str, $amount) {
        if ($amount === 0) {
            return $str;
        }
        $amount = $amount % strlen($str);
        return substr($str, $amount) . substr($str, 0, $amount);
    }

    public static function linkIdToShortCode(int $id) {

        $code = '';
        while ($id > 0) {

            $code = static::SHORTCODE_BASE_CHARS[$id % static::SHORTCODE_BASE] . $code;

            $id = floor($id / static::SHORTCODE_BASE);

        }

        // $code = self::CHARS[$id] . $code;

        // return $code;

        $rotateAmount = -floor(strlen($code) / 2);

        return self::rotateString($code, $rotateAmount);

    }

    public static function shortCodeToId($shortCode) {

        $limit = strlen($shortCode);

        if ($limit === 0) {
            return null;
        }

        if ($limit > 10) {
            return null;
        }

        $shortCode = self::rotateString($shortCode, floor($limit / 2));

        $res = strpos(static::SHORTCODE_BASE_CHARS, $shortCode[0]);

        if ($res === false) {
            return null;
        }

        for ($i = 1; $i < $limit; $i++) {

            $pos = strpos(static::SHORTCODE_BASE_CHARS, $shortCode[$i]);

            if ($pos === false) {
                return null;
            }

            $res = (static::SHORTCODE_BASE * $res) + $pos;

        }

        return $res;

    }

}