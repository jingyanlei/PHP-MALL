<?php
namespace interfaces;

/**
 * 库存接口类
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/11/29
 * Time: 16:36
 */
interface Inventory {

    /**
     * 验证库存并减少库存
     * @param array $parmas
     * @return bool
     */
    public function check(array $parmas);

}