<?php

declare(strict_types=1);

namespace Tests\Services;

use Mockery;
use Mockery\MockInterface;
use dkhorev\LaravelAmpq\Contracts\AmpqClientContract;
use dkhorev\LaravelAmpq\Contracts\SendToTopicServiceContract;
use dkhorev\LaravelAmpq\Services\SendToTopicService;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Tests\TestCase;

class SendToAmpqTopicServiceTest extends TestCase
{
    public function testInstantiate(): void
    {
        /** @var SendToTopicServiceContract $service */
        $service = app(SendToTopicService::class);

        $this->assertInstanceOf(SendToTopicService::class, $service);
        $this->assertInstanceOf(SendToTopicServiceContract::class, $service);
    }

    public function testSendsMessage(): void
    {
        // arrange
        $this->mock(
            AmpqClientContract::class,
            static function (MockInterface $mock) {
                $mockChannel = Mockery::mock(AMQPChannel::class);
                $mockChannel->allows(
                    [
                        'exchange_declare' => null,
                        'close'            => null,
                    ]
                );

                // assert
                $mockChannel->shouldReceive('basic_publish')
                            ->once()
                            ->with(AMQPMessage::class, 'test_exchange', 'test_topic');


                $mockConnection = Mockery::mock(AMQPStreamConnection::class);
                $mockConnection->allows(
                    [
                        'channel' => $mockChannel,
                        'close'   => null,
                    ]
                );

                $mock->allows(
                    [
                        'getConnection' => $mockConnection,
                    ]
                );
            }
        );
        $client = app(AmpqClientContract::class);

        // act
        /** @var SendToTopicServiceContract $service */
        $service = app(SendToTopicService::class);
        $result = $service->postTo(
            $client,
            'test_exchange',
            'test_topic',
            ['some data']
        );

        // assert
        $this->assertTrue($result);
    }
}
