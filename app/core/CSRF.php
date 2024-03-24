<?php
namespace App\core;

class CSRF  {
    public static function create_token()  {                         // метод генерации токена
        $token = hash('gost-crypto', random_int(0,999999));         
        $_SESSION['token1'] = $token;                               // в проекте используется token (для VK) и token1 (для проверки авторизации)
        return $token;
    }

    public static function validate($token) {                       // метод проверки токена
        return isset($_SESSION['token1']) && $_SESSION['token1'] == $token;
    }
}