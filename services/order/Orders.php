<?php
namespace services\order;

/**
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/11/29
 * Time: 15:13
 */
class Orders {

    //库存类
    private $_inventory;
    private $_db;

    //构造方法
    public function __construct() {
        global $db;
        $inventory = null;
        //使用innerdb验证库存或redis验证库存
        switch(INVENTORY_TYPE) {
            case 'redis':
                $inventory = new \services\inventory\redis\Inventory();
                break;
            case 'db2':
                $inventory = new \services\inventory\db2\Inventory();
                break;
            default:
                $inventory = new \services\inventory\db\Inventory();
        }
        $this->_inventory = $inventory;
        $this->_db = $db;
    }

    /**
     * 添加订单
     * @param array $params
     * @return bool
     */
    public function add(array $params) {
        //验证库存并扣减库存
        $result = $this->_inventory->sub($params);
        if ($result) {
            $order_num = implode(NULL, array_map('ord', str_split(substr(uniqid(), 2, 13), 1)));
            $insertStatement = $this->_db->insert(['order_num', 'goods_id', 'goods_name', 'create_time'])
                ->into('orders')
                ->values(array($order_num, $params['goods_id'], $params['goods_name'], time()));
            $insertId = $insertStatement->execute();
            return ($insertId > 0) ? true : false;
        } else {
            return false;
        }
    }

}