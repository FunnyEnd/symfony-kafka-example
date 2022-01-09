<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\KafkaTopic;
use App\Service\AsyncAction\Message;
use App\Service\AsyncAction\TopicProducerFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppKafkaProducerCommand extends Command
{
    private const COUNT_ARG = 'count';

    private TopicProducerFactory $topicProducerFactory;

    public function __construct(
        TopicProducerFactory $topicProducerFactory,
        string               $name = null
    ) {
        parent::__construct($name);

        $this->topicProducerFactory = $topicProducerFactory;
    }

    protected function configure(): void
    {
        $this->setDescription('Kafka producer command')
            ->setHelp('Kafka producer command')
            ->addArgument(self::COUNT_ARG, InputArgument::OPTIONAL, 'Number of messages', 1);;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = $input->getArgument(self::COUNT_ARG);
        $producer = $this->topicProducerFactory->make();
        $topicProducer = $producer->newTopic(KafkaTopic::EMAIL_NOTIFICATION->value);

        $i = 0;
        do {
            $messageId = Uuid::uuid4()->toString();
            $message = new Message($messageId, KafkaTopic::EMAIL_NOTIFICATION, 'event', 'TestKafKaPRoducer');
            $topicProducer->produce(RD_KAFKA_PARTITION_UA, 0, $message->toJson(), $messageId);
            $producer->poll(0);
        } while (++$i < $count);

        while ($producer->getOutQLen() > 0) {
            $producer->poll(10);
        }

        $io->success('Produced ' . $count . ' messages.');

        return Command::SUCCESS;
    }
}