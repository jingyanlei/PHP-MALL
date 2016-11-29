<?php
namespace services\inventory\db;
use services\goods\Goods;

/**
 * db库存类
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/11/29
 * Time: 15:16
 */
class Inventory implements \interfaces\Inventory {

    /**
     * 验证库存并减少库存
     * @param array $params
     * @return bool
     */
    public function check(array $params) {
        $goods = new Goods();
        $data = $goods->getInventory($params);
        //查询
        if (!empty($data) && is_array($data) && $data['inventory'] > $params['num']) {
            //减库存
            return $goods->subInventory($params);
        } else {
            return false;
        }
    }

}