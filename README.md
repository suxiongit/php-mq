# php-mq

PHP 模拟消息队列

[TOC]

## 环境准备

- PHP（推荐使用 php7+）
- MySQL
- Redis
- Composer

我们使用 PHP 作为编程语言，来操作消息队列。

另外，分别使用 MySQL、Redis 来作为消息队列的队列容器。

使用 Composer 实现类的自动加载。

## 目录结构

```
php-mq
|--- src
    |--- Driver
        |--- QueueI.php
        |--- MysqlDriver.php
        |--- RedisDriver.php
        |--- Job.php
    |--- Queue.php
|--- tests
	|--- Producer.php
	|--- Consumer.php
	|--- RedisProducer.php
	|--- RedisConsumer.php
|--- queue.sql
```

文件作用介绍：

- Producer.php、RedisProducer.php

  生产者，用于生成消息。

- Consumer.php、RedisConsumer.php

  消费者，用于消费消息。

- Queue.php

  队列操作类，为生产者和消费者提供一组统一的消息队列操作接口。

- Driver/QueueI.php

  队列操作接口，规范为 Queue.php 提供具体服务的驱动类，所有驱动类必须实现此接口，以确保有能力为 Queue.php 提供底层服务。

- Driver/MysqlDriver.php

  MySQL 队列操作驱动，负责与 MySQL 的底层操作。

- Driver/RedisDriver.php

  Redis 队列操作驱动，负责与 Redis 的底层操作。

- Driver/Job.php

  统一的消息结构（数据格式），就像是面向对象中的接口，使调用方和实现方，都在规范轨迹之内工作。

- queue.sql

  这是一个 SQL 文件，里面是 MySQL 消息队列表的表结构。

## 支持 Composer

在根目录新建 composer.json 文件，描述项目的依赖关系。

```json
{
  "require": {
    "php": ">=7.0.0"
  },
  "minimum-stability": "dev",
  "autoload": {
    "psr-4": {
      "app\\": "src/"
    }
  }
}
```

接下来运行以下命令进行初始化&安装依赖：

```
composer install
```

生成&更新 composer.lock 和 vendor/autoload.php（引入这个文件实现自动加载）。

## 测试步骤

- 打开 4 个 client（cmd，terminal）窗口

- 在其中两个命令行中，分别运行如下命令：

  ```
  php Consumer.php // 运行 MySQL 消费者
  php RedisConsumer.php // 运行 Redis 消费者
  ```

  运行成功后，这两个窗口将处于运行状态，两个消费者都会始终处于 reserve 循环中，直到接收数据，才会输出。

- 再另外两个窗口，运行如下命令：

  ```
  php Producer.php
  php RedisProducer.php
  ```

## 小结

这只是一个简单的消息队列模拟案例，它帮助我们理解消息队列，但并不能应用于实际项目中。

我们可以推理一下，消息队列，本该可以使用在高并发的场景，但我们现在实现的消息队列，它还有许多漏洞，根本无法应用在实战场景中。

比如：

- mysql 驱动的 reserve 方法，它先接收，接收成功后再修改 job 的状态，这在高并发的情况下，将导致我们的 job 被重复接收，因为在「接收」和「修改状态」两个环节中间，可能会有另一个「请求」进来，此时的 job 还处于 ready 状态。
- redis 驱动的 reserve 方法，使用了 redis 的 rPop 操作，这个方法，会在取出数据的同时，将数据从 list 中删除，那么，如果我们消费者处理失败，这个数据就有可能丢失。——参考 brpoplpush。
- redis 驱动的 put 方法，使用了 lPush 操作，此操作执行成功后，将会返回 list 的长度，我们的 redis 驱动会使用这个长度来作为该 job 的下标（ id ）。然而， list 的长度会不断变化，并发场景下，你 put 成功后，可能还在处理其他流程，此进程并未结束，此时若有消费者接收走一个消息，则 list 的长度就发生了变化，你在 put 时的 id 就无法与 list 中的数据对应起来。



参考文章[《深入浅出PHP消息队列》](https://www.kancloud.cn/vson/php-message-queue)。