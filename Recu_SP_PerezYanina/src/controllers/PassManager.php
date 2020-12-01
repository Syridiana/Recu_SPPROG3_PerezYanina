<?php

namespace App\Controllers;

require '../vendor/autoload.php';

class PassManager {

    public static function Create(string $pass)
    {
        return hash('SHA512', $pass);
    }

}