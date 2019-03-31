<?php
/**
 * RedisProducer.php
 * Description: 生产者
 * Time: 2019/3/21 6:36 AM
 * Author: suxiong
 */

require 'vendor/autoload.php';

use app\Queue;
use app\driver\Job;

try {
    // 队列初始化
    Queue::init('Redis', [
        'ip' => 'localhost',
        'port' => 6379,
        'tubes' => 'tubes',
    ]);

    // 生产者放入消息
    $job = new Job([
        'job_data' => json_encode([
            'order_id' => time(),
            'user_id' => 10001,
        ]),
        'tube' => 'default',
    ]);
    $job = Queue::put($job);
    echo $job->id . PHP_EOL;

} catch (\Exception $e) {
    var_dump($e->getMessage());
}