<?php
namespace Console\App;

class HmacCalculator
{
    public function calculateHmac($message, $key)
    {
        $hmac = hash_hmac('sha3-256',$message, $key);

        return $hmac;
    }
}