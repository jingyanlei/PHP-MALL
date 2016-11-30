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

    private $_db;

    public function __construct() {
        global $db;
        $this->_db = $db;
    }

    /**
     * 验证库存并减少库存
     * @param array $params
     * @return bool
     */
    public function sub(array $params) {
        $goods = new Goods();
        $this->_db->beginTransaction();
        try {
            $data = $goods->getInventory($params);
            sleep(2); //停2秒,方便测试出问题
            //查询
            if (!empty($data) && is_array($data)) {
                //减库存
                if ($goods->subInventory($params)) {
                    $this->_db->commit();
                    return true;
                } else {
                    $this->_db->rollBack();
                }
                return false;
            } else {
                $this->_db->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            $this->_db->rollBack();
            return false;
        }
    }

}