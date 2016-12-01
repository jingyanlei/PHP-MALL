<?php
define('DSN', 'mysql:host=127.0.0.1;dbname=mall;charset=utf8');
define('USR', 'root');
define('PWD', '123456aA!');
//db 悲观锁(事务级别SERIALIZABLE) db2 mysql乐观锁 redis redis方式
define('INVENTORY_TYPE', 'db2');

spl_autoload_register(function($class) {
    $class = str_replace("\\", "/", $class);
    $file = __DIR__.'/'.$class.'.php';
    if (is_file($file)) {
        require_once($file);
    }
});

$db = new \Slim\PDO\Database(DSN, USR, PWD);