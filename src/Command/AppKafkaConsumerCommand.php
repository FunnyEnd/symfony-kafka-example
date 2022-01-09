<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\KafkaTopic;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppKafkaConsumerCommand extends Command
{
    private string $brokers;

    public function __construct(
        string $brokers,
        string $name = null,
    ) {
        parent::__construct($name);

        $this->brokers = $brokers;
    }

    protected function configure(): void
    {
        $this->setDescription('Kafka consumer command')
            ->setHelp('Kafka consumer command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $conf = new Conf();
        $conf->setRebalanceCb(function (KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    $kafka->assign($partitions);
                    break;
                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $kafka->assign();
                    break;
                default:
                    throw new \Exception($err);
            }
        });

        $conf->set('group.id', 'myConsumerGroup');
        $conf->set('metadata.broker.list', $this->brokers);
        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new \RdKafka\KafkaConsumer($conf);
        $consumer->subscribe([KafkaTopic::EMAIL_NOTIFICATION->value]);

        while (true) {
            $message = $consumer->consume(120*1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    // perform code
                    $io->info($message->payload);
                    $consumer->commit($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $io->info('EOF');
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $io->warning('TIMED_OUT');
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
            }
        }
    }
}
