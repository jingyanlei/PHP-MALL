<?php
namespace services\goods;

/**
 * 商品类
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/11/29
 * Time: 15:14
 */
class Goods {

    private $_db;

    public function __construct() {
        $this->_db = new \Slim\PDO\Database(DSN, USR, PWD);
    }

    /**
     * 增加库存
     * @param array $params
     * @return bool
     */
    public function addInventory(array $params) {
        return true;
    }

    /**
     * @param array $parmas
     * @return int
     */
    public function getInventory(array $parmas) {
        $selectStatement = $this->_db->select(['inventory'])
            ->from('goods')
            ->where('id', '=', $parmas['goods_id'])
            ->where('inventory', '>', $parmas['num'], 'AND');
        $stmt = $selectStatement->execute();
        return $stmt->fetch();
    }

    /**
     * 减少库存
     * @param array $params
     * @return bool
     */
    public function subInventory(array $params) {
        $sql = 'UPDATE goods SET inventory = inventory - '.$params['num'].' WHERE id = '.$params['goods_id'].'';
        $query = $this->_db->query($sql);
        if ($query !== false && $query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

}