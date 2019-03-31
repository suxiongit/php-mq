<?php
/**
 * RedisConsumer.php
 * Description: 消费者
 * Time: 2019/3/21 9:16 AM
 * Author: suxiong
 */

require 'vendor/autoload.php';

use app\Queue;

try {
    // 队列初始化
    Queue::init('Redis', [
        'ip' => 'localhost',
        'port' => 6379,
        'tubes' => 'tubes',
    ]);

    while (1) {
        // 死循环，使进程一直在 cli 中运行，不断从消息队列读取数据
        $job = Queue::reserve('default');
        if (!$job->isEmpty()) {
            echo $job->job_data . PHP_EOL;
            sleep(2);
            if (Queue::delete($job)) {
                echo 'job was deleted' . PHP_EOL;
            } else {
                echo 'delete failed' . PHP_EOL;
            }
        }
    }

} catch (\Exception $e) {
    var_dump($e->getMessage());
}