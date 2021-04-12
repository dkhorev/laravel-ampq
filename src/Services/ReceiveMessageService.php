<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use dkhorev\LaravelAmpq\Contracts\AmpqClientContract;
use dkhorev\LaravelAmpq\Contracts\ReceiveMessageServiceContract;

class ReceiveMessageService implements ReceiveMessageServiceContract
{
    /**
     * Listen data from all topic/exchange on stack
     *
     * @param AmpqClientContract $client
     * @param string             $listenStack
     */
    public function listenStack(AmpqClientContract $client, string $listenStack): void
    {
        try {
            $connection = $client->getConnection();

            $channel = $connection->channel();

            $stack = collect(config("laravel-ampq.callbacks.{$listenStack}"));

            $stack->keys()->each(
                static function (string $exchange) use ($stack, $channel) {
                    $topics = collect($stack->get($exchange));

                    $channel->exchange_declare($exchange, 'topic', false, false, false);

                    $topics->keys()->each(
                        static function (string $topic) use ($channel, $exchange, $topics) {
                            echo "Ampq listening: {$exchange}.{$topic}\n";
                            $callback = resolve($topics->get($topic));
                            [$queueName] = $channel->queue_declare('', false, false, true, false);
                            $channel->queue_bind($queueName, $exchange, $topic);
                            $channel->basic_consume($queueName, '', false, true, false, false, $callback);
                        }
                    );
                }
            );

            while ($channel->is_consuming()) {
                $channel->wait();
            }

            $channel->close();
            $connection->close();
        } catch (Exception $e) {
            Log::error('ReceiveMessageService: ' . $e->getMessage());
        }
    }
}
