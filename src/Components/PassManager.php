<?php

namespace Components;

class PassManager
{
    public static function Hash(string $pass)
    {
        return hash('HS256', $pass);
      // return hash('sha256', $pass);
    }
}
