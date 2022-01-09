<?php

declare(strict_types=1);

namespace App\Service\AsyncAction;

use RdKafka\Conf;
use RdKafka\Producer;

class TopicProducerFactory
{
    private string $brokers;

    public function __construct(string $brokers)
    {
        $this->brokers = $brokers;
    }

    public function make(): Producer
    {
        $conf = new Conf();
        $conf->set('log_level', (string)LOG_DEBUG);
        $conf->set('debug', 'all');
        $conf->set('api.version.request', 'true');
        $conf->set('message.send.max.retries', '5');
        $conf->set('bootstrap.servers', $this->brokers);
        $conf->set('metadata.broker.list', $this->brokers);
        $kafkaProducer = new Producer($conf);
        $kafkaProducer->addBrokers($this->brokers);

        return $kafkaProducer;
    }
}