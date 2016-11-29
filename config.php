<?php
define('DSN', 'mysql:host=127.0.0.1;dbname=mall;charset=utf8');
define('USR', 'root');
define('PWD', '123456aA!');

spl_autoload_register(function($class) {
    $class = str_replace("\\", "/", $class);
    $file = __DIR__.'/'.$class.'.php';
    if (is_file($file)) {
        require_once($file);
    }
});