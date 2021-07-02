<?php
/**
 * Consumer.php
 * Description: 消费者
 * Time: 2019/3/21 6:12 AM
 * Author: suxiong
 */

require '../vendor/autoload.php';

use app\Queue;

try {
    // 队列初始化
    Queue::init('Mysql', [
        'dsn' => 'mysql:host=localhost;dbname=php-mq',
        'username' => 'root',
        'password' => '',
        'table' => 'queue',
        'ttr' => 60,
    ]);

    while (1) {
        // 死循环，使进程一直在 cli 中运行，不断从消息队列读取数据
        $job = Queue::reserve('test');
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