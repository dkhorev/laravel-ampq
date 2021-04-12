<?php

declare(strict_types=1);

namespace Tests\Services;

use Mockery;
use Mockery\MockInterface;
use dkhorev\LaravelAmpq\Contracts\AmpqClientContract;
use dkhorev\LaravelAmpq\Contracts\ReceiveMessageServiceContract;
use dkhorev\LaravelAmpq\Services\ReceiveMessageService;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Tests\TestCase;

class ReceiveMessageServiceTest extends TestCase
{
    /**
     * @test
     */
    public function testInstantiate(): void
    {
        /** @var ReceiveMessageService $service */
        $service = app(ReceiveMessageService::class);

        $this->assertInstanceOf(ReceiveMessageService::class, $service);
        $this->assertInstanceOf(ReceiveMessageServiceContract::class, $service);
    }

    /**
     * @test
     */
    public function testConsumesMessages(): void
    {
        // arrange
        config()->set(
            'laravel-ampq.callbacks.test_stack',
            [
                'test_exchange' => [
                    'test_topic' => 'SomeCallbackClass',
                ],
            ]
        );
        $this->mock(
            AmpqClientContract::class,
            function (MockInterface $mock) {
                $mockChannel = Mockery::mock(AMQPChannel::class);

                $mockChannel->shouldReceive('exchange_declare')
                            ->with('test_exchange', 'topic', false, false, false)
                            ->once();

                $mockChannel->shouldReceive('queue_declare')
                            ->with("", false, false, true, false)
                            ->once()
                            ->andReturn(['queue_name']);

                $mockChannel->shouldReceive('queue_bind')
                            ->with('queue_name', 'test_exchange', 'test_topic')
                            ->once();

                // mocking callback config
                $mockCallback = new class {
                };
                $this->app->bind(
                    'SomeCallbackClass',
                    function () use ($mockCallback) {
                        return $mockCallback;
                    }
                );
                $mockChannel->shouldReceive('basic_consume')
                            ->once()
                            ->with('queue_name', '', false, true, false, false, $mockCallback);

                $mockChannel->shouldReceive('is_consuming')
                            ->once()
                            ->andReturnFalse();

                $mockChannel->allows(
                    [
                        'close' => null,
                    ]
                );

                $mockConnection = Mockery::mock(AMQPStreamConnection::class);
                $mockConnection->allows(
                    [
                        'channel' => $mockChannel,
                        'close'   => null,
                    ]
                );

                $mock->shouldReceive('getConnection')
                     ->once()
                     ->andReturn($mockConnection);
            }
        );
        $client = app(AmpqClientContract::class);

        /** @var ReceiveMessageService $service */
        $service = app(ReceiveMessageService::class);
        $service->listenStack($client, 'test_stack');
    }
}
