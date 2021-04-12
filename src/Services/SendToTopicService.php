<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Services;

use dkhorev\LaravelAmpq\Contracts\AmpqClientContract;
use dkhorev\LaravelAmpq\Contracts\SendToTopicServiceContract;
use Exception;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class SendToTopicService implements SendToTopicServiceContract
{
    /**
     * Post data to AMPQ topic
     *
     * @param AmpqClientContract $client
     * @param string             $exchange
     * @param string             $topic
     * @param array              $data
     *
     * @return bool
     */
    public function postTo(AmpqClientContract $client, string $exchange, string $topic, array $data): bool
    {
        try {
            $connection = $client->getConnection();

            $channel = $connection->channel();

            $channel->exchange_declare($exchange, 'topic', false, false, false);

            $msg = new AMQPMessage(json_encode($data, JSON_THROW_ON_ERROR, 512));

            $channel->basic_publish($msg, $exchange, $topic);

            $channel->close();

            $connection->close();

            return true;
        } catch (Exception $e) {
            Log::error('SendToTopicService: ' . $e->getMessage());

            return false;
        }
    }
}
