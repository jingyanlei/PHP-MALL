<?php
use services\order\Orders;
/**
 * 商城抢购库存超卖解决方案测试
 * Created by PhpStorm.
 * User: jingyanlei
 * Date: 2016/11/29
 * Time: 14:38
 */
require_once('./vendor/autoload.php');
require_once('./config.php');

//模拟下订单流程
//1.验证商品库存
//2.生成订单
$params = [
    'goods_id'=>1,
    'goods_name'=>'测试商品',
    'num'=>1
];
$order = new Orders();
$result = $order->add($params);
if ($result) {
    echo '成功';
} else {
    echo '失败';
}