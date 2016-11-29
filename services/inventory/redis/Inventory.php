<?php
namespace services\inventory\redis;

/**
 * redis库存类
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/11/29
 * Time: 15:16
 */
class Inventory implements \interfaces\Inventory {

    /**
     * 验证库存并减少库存
     * @param array $parmas
     * @return bool
     */
    public function check(array $parmas) {
        return true;
    }

}