# PHP-MALL
## 1.商城抢购，秒杀库存超卖是比较头疼的事，下面使用三种方法防止超卖
```
1.mysql锁机制，悲观锁InnoDB行级锁方案，不建议使用，对数据库压力较大，如果出现死锁会导致一直不能更新，除非kill掉进程
2.mysql乐观锁 不使用第三方情况下可以使用此方案
3.redis incrby decrby原子性操作，防止超卖
4.为方便扩展，把库存类抽象出接口，方便以后扩展，也可以使用其它方式实现
```

### 1.1.mysql锁机制，悲观锁，InnoDB行级锁方案，查询需使用索引

```
1.事务级别必须为 SERIALIZABLE 级别
2.查询条件验证库存是否够本次购买，例： id = 1 AND inventory >=1
3.PDO update更新后，不但要验证返回状态是否为!==false,并且同时验证影响行数是否大于0
4.数据库链接一定要使用同一链接，单例或DB链接传入，建议使用单例，由于测试网上找了个db类，没有实现单例，所以使用比较笨的方法，传递数据库链接
5.update件件增加验证购买数量条件 AND inventory >=1
```

### 1.2.mysql乐观锁

```
数据库表增加版本字段如version，每次修改时版本号+1
如果更新操作顺序执行，则数据的版本（version）依次递增，不会产生冲突。但是如果发生有不同的业务操作对同一版本的数据进行修改，那么，先提交的操作（图中B）会把数据version更新为2，当A在B之后提交更新时发现数据的version已经被修改了，那么A的更新操作会失败。
PDO update更新后，不但要验证返回状态是否为true,并且同时验证影响行数是否大于0
```

### 1.3.redis原子性操作

```
incr/decr原子性操作，incr增加，decr减少
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
使用redis测试时，每次修改完库存需要删除KEY：DEL goods_1
```

## 2.测试
```
配置使用方式
//db 悲观锁(事务级别SERIALIZABLE) db2 mysql乐观锁 redis redis方式
define('INVENTORY_TYPE', 'db2');

并发小时可能不会有问题，如查并发较大会有超卖现像，为了可以重现超卖下面代码加入2秒迟时
services/inventory/db/Inventory.php 31行左右
$data = $goods->getInventory($params);
sleep(2); //停2秒,方便测试出问题
nginx配置站点mall.com
本机hosts绑定域名 10.211.55.100 mall.com(10.211.55.100为实际站点的ip)

测试方法使用ab测试，请求100，并发10
ab -n100 -c10 http://mall.com
```
## 3.问题
```
注意
由于订单流程中，把验证库存和扣减库存放在了下订单前，可以减少下订单数量和开启的事务数，但如果用户在扣减库存后下订单过程中失败，会出现少卖现象
如果对库存要求不高，可以不用考虑，如果对库存要求较高，需要把扣减成功下单失败对应的订单保存到日志中，然后异步处理恢复库存或给用户补单
```
