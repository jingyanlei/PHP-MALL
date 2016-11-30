# PHP-MALL
## 商城抢购，秒杀库存超卖是比较头疼的事，下面使用两种方法防止超卖
一、mysql锁机制，悲观锁，InnoDB行级锁方案，查询需使用索引

```
1.事务级别必须为 SERIALIZABLE 级别
2.查询条件验证库存是否够本次购买，例： id = 1 AND inventory >=1
3.PDO update更新后，不但要验证返回状态是否为true,并且同时验证影响行数是否大于0
4.数据库链接一定要使用同一链接，单例或DB链接传入，建议使用单例，由于测试网上找了个db类，没有实现单例，所以使用比较笨的方法，传递数据库链接
```
```
并发小时可能不会有问题，如查并发较大会有超卖现像，为了可以重现超卖下面代码加入2秒迟时
services/inventory/db/Inventory.php 31行左右
$data = $goods->getInventory($params);
sleep(2); //停2秒,方便测试出问题
测试方法使用ab测试，请求100，并发10
ab -n100 -c10 http://mall.com
```
