<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class CustomEncryptor
{
    protected $encrypter;

    public function __construct()
    {
        $key = base64_decode(config('app.custom_encryption_key'));
        $this->encrypter = new Encrypter($key, 'AES-256-CBC');
    }

    public function encrypt($value)
    {
        return $this->encrypter->encrypt($value);
    }

    public function decrypt($value)
    {
        return $this->encrypter->decrypt($value);
    }
}