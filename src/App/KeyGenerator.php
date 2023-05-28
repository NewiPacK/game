<?php
namespace Console\App;

class KeyGenerator
{
    public function generateKey()
    {

        $key = bin2hex(random_bytes(32));

        return $key;
    }
}