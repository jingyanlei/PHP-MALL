<?php
namespace services\inventory\redis;
use services\goods\Goods;

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
    public function sub(array $parmas) {
        $redis_client = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
        $key = 'goods_'.$parmas['goods_id'];
        //库存不存在设置存存
        sleep(2); //停2秒,方便测试出问题
        if ($redis_client->exists($key) == '0') {
            $goods = new Goods();
            $data = $goods->getInventory($parmas);
            if (!empty($data) && is_array($data)) {
                $redis_client->set($key, $data['inventory']);
            }
        }
        //此处不可以取出后放入php变量判断库存,否则会出现幻读,导致超卖
        if ($redis_client->decrby($key, $parmas['num']) > -1) {
                //减库存
                $goods = new Goods();
                $parmas['version'] = 1;
                return $goods->subInventory($parmas);
        } else {
            //购买多个时,如库存不足,需要把数量加回去,否则会出现库减库存,商品并没有卖出去
            $redis_client->incrby($key, $parmas['num']);
            return false;
        }
    }

}