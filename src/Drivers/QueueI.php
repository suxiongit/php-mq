<?php
/**
 * QueueI.php
 * Description: queue interface
 * Time: 2019/2/23 1:52 PM
 * Author: suxiong
 */

namespace app\Drivers;

interface QueueI
{
    /**
     * @return array
     * 查询 tubes 列表
     * 一个系统需要多个队列，它们可能分别用于存储短信、邮件等，它们互相隔离。
     */
    public function tubes(): array;

    /**
     * @param Job $job
     * @return Job
     * 向队列中存储一个消息（任务）
     */
    public function put(Job $job): Job;

    /**
     * @param string $tube 需要指定从哪个队列接收任务
     * @return Job
     * 从队列接收一个消息（任务）
     */
    public function reserve(string $tube): Job;

    /**
     * @param Job $job
     * @return bool
     * 删除某个消息（任务）
     */
    public function delete(Job $job): bool;

    /**
     * @param string $tube
     * @return array
     * 获取某个队列中的消息列表
     */
    public function jobs(string $tube): array;

}