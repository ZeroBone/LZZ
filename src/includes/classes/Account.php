<?php


namespace Lzz\Account;

use Lzz\Utils\Utils;

class Account {

    const NAME_MAX_LENGTH = 16;
    const PASSWORD_MAX_LENGTH = 50;
    const EMAIL_MAX_LENGTH = 40;

    const JWT_SECRET = '<128-byte key>';

    private $checked = false;

    private $loggedIn;

    public $tokenPayload;

    public function loggedIn() {

        if ($this->checked) {

            return $this->loggedIn;

        }

        if (!isset($_COOKIE['zl'])) {
            $this->checked = true;
            $this->loggedIn = false;
            return false;
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $_COOKIE['zl'], 3);

        $userSignature = Utils::base64UrlDecode($signatureEncoded);

        $realSignature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            static::JWT_SECRET,
            true
        );

        $this->checked = true;

        $this->loggedIn = hash_equals($realSignature, $userSignature);

        if ($this->loggedIn) {

            $this->tokenPayload = json_decode(Utils::base64UrlDecode($payloadEncoded), true);

        }

        return $this->loggedIn;

    }

    public static function generateToken($payload) {

        $header = json_encode([
            'algo' => 'HS256',
            'c' => time()
        ]);

        // $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlHeader = Utils::base64UrlEncode($header);

        // $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $base64UrlPayload = Utils::base64UrlEncode($payload);

        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . '.' . $base64UrlPayload,
            static::JWT_SECRET,
            true
        );

        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . Utils::base64UrlEncode($signature);

    }

    public static function hashPassword($password, $salt) {

        return hash('sha512', 'h&dh*jG0e' . $password . 'dY2j)shE' . $salt . 'hVzlkSnR');

    }

}