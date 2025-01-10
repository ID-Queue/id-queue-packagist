<?php

namespace IdQueue\IdQueuePackagist\Traits;

use Illuminate\Support\Str;

trait Encryptable
{
    private $encryptionKey;

    private $iv;

    public function __construct()
    {
        $this->encryptionKey = env('ENCRYPTION_KEY'); // Get from .env
        $this->iv = hex2bin(env('IV')); // Convert from hex to binary
    }

    /**
     * Encrypt the given token.
     *
     * @param  string  $token
     * @return string
     */
    public function encryptToken($token)
    {
        return openssl_encrypt($token, 'aes-256-cbc', $this->encryptionKey, 0, $this->iv);
    }

    /**
     * Decrypt the given token.
     *
     * @param  string  $encryptedToken
     * @return string|false
     */
    public function decryptToken($encryptedToken)
    {
        return openssl_decrypt($encryptedToken, 'aes-256-cbc', $this->encryptionKey, 0, $this->iv);
    }

    /**
     * Generate a new secure token.
     *
     * @return string
     */
    public function generateSecureToken()
    {
        return Str::random(40); // You can customize the length
    }
}
