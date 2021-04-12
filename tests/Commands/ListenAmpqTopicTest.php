<?php

namespace Tests\Commands;

use Mockery\MockInterface;
use dkhorev\LaravelAmpq\Clients\AmpqClient;
use dkhorev\LaravelAmpq\Commands\ListenAmpqTopicCommand;
use dkhorev\LaravelAmpq\Services\ReceiveMessageService;
use Tests\TestCase;

class ListenAmpqTopicTest extends TestCase
{
    public function testInstantiate(): void
    {
        $cmd = app(ListenAmpqTopicCommand::class);

        $this->assertInstanceOf(ListenAmpqTopicCommand::class, $cmd);
    }

    public function testStartsListen(): void
    {
        // arrange
        config()->set(
            'laravel-ampq.servers.test_connection',
            [
                'host'     => 'AMPQ_HOST',
                'user'     => 'AMPQ_USER',
                'password' => 'AMPQ_PASSWORD',
                'port'     => 'AMPQ_PORT',
            ]
        );
        $this->mock(
            ReceiveMessageService::class,
            static function (MockInterface $mock) {
                $mock->shouldReceive('listenStack')
                     ->with(AmpqClient::class, 'test_stack')
                     ->once();
            }
        );

        // act
        $this->artisan('ampq:listen test_connection test_stack');
    }
}
