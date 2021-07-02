<?php
/**
 * Producer.php
 * Description: 生产者
 * Time: 2019/3/21 5:58 AM
 * Author: suxiong
 */

require '../vendor/autoload.php';

use app\Queue;
use app\Drivers\Job;

try {
    // 队列初始化
    Queue::init('Mysql', [
        'dsn' => 'mysql:host=localhost;dbname=php-mq',
        'username' => 'root',
        'password' => '',
        'table' => 'queue',
        'ttr' => 60,
    ]);

    // 生产者放入消息
    $job = new Job([
        'job_data' => json_encode([
            'order_id' => time(),
            'user_id' => 10001,
        ]),
        'tube' => 'test',
    ]);
    $job = Queue::put($job);

} catch (\Exception $e) {
    var_dump($e->getMessage());
}