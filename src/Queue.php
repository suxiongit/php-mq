<?php
/**
 * Queue.php
 * Description: 队列工具
 * Time: 2019/2/23 2:21 PM
 * Author: suxiong
 */

namespace app;

use app\driver\Job;

class Queue
{
    public static $driver = null;

    /**
     * @param string $driver
     * @param array $options
     * 初始化
     */
    public static function init($driver = 'Mysql', $options = []) {
        // 通过变量 new 对象，需写全命名空间
        $class = "app\\driver\\{$driver}Driver";
        self::$driver = new $class($options);
    }

    public static function tubes(): array {
        return self::$driver->tubes();
    }

    public static function put(Job $job): Job {
        return self::$driver->put($job);
    }

    public static function reserve(string $tube = 'default'): Job {
        return self::$driver->reserve($tube);
    }

    public static function jobs(string $tube = 'default'): array {
        return self::$driver->jobs($tube);
    }

    public static function delete(Job $job): bool {
        return self::$driver->delete($job);
    }

}