<?php


namespace Lzz\Utils;

use PDO;

class Utils {

    private static $db = null;

    public static function getDatabase(): PDO {

        if (static::$db == null) {

            // $dbConfig = require_once $_SERVER['DOCUMENT_ROOT'] . '/dbconfig.php';
            $dbConfig = require_once __DIR__ . '/../../dbconfig.php';

            static::$db = new PDO(
                'mysql:host='.$dbConfig['host'].';charset=utf8;dbname='.$dbConfig['name'],
                $dbConfig['user'],
                $dbConfig['pass']
            );

            return static::$db;

        }

        return static::$db;

    }

    public static function escapeHTML($text) {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    public static function getUserIpAddress() {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public static function headerError($errorText) {
        echo '<div class="center"><div class="error">'.$errorText.'</div></div>';
    }

    public static function headerInfo($infoText='') {
        echo '<div class="center"><div class="info">'.$infoText.'</div></div>';
    }

    public static function headerSuccess($successText) {
        echo '<div class="center"><div class="success">'.$successText.'</div></div>';
    }

    public static function headerWarning($warningText='') {
        echo '<div class="center"><div class="warning">'.$warningText.'</div></div>';
    }

    public static function rstrlen($line) {
        return mb_strlen($line, 'utf-8');
    }

    public static function startsWith($haystack, $needle) {
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }

    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data) {

        return base64_decode(str_pad(
            strtr($data, '-_', '+/'),
            strlen($data) % 4,
            '=',
            STR_PAD_RIGHT
        ));
    }

    const CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789';

    public static function generateCode($length) {

        $code = '';

        $clen = strlen(static::CHARS) - 1;

        while (strlen($code) < $length) {
            try {
                $code .= static::CHARS[random_int(0, $clen)];
            }
            catch (\Exception $e) {
                $code .= static::CHARS[mt_rand(0, $clen)];
            }
        }

        return $code;
    }

    public static function validateAndCleanUrl(&$url) {

        if (!static::startsWith($url, 'http://') && !static::startsWith($url, 'https://')) {
            $url = 'http://' . $url;
        }

        $url = filter_var($url, FILTER_SANITIZE_URL);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme']) || ($parsedUrl['scheme'] !== 'http' && $parsedUrl['scheme'] !== 'https')) {
            return false;
        }

        return isset($parsedUrl['host']) /*&& !isset($parsedUrl['port'])*/ && !isset($parsedUrl['user']) && !isset($parsedUrl['pass']);// && isset($parsedUrl['path']);

    }

}