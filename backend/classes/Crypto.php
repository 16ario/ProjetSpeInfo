<?php
class Crypto {
    private static string $key;

    public static function init(string $base64Key): void {
        self::$key = base64_decode($base64Key);
    }

    public static function encrypt(string $data): string {
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($data, 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $ciphertext);
    }

    public static function decrypt(string $encoded): string {
        $raw = base64_decode($encoded);
        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);
        return openssl_decrypt($ciphertext, 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, $iv);
    }
}
