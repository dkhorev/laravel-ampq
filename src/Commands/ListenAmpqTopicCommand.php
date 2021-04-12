<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Commands;

use dkhorev\LaravelAmpq\Clients\AmpqClient;
use dkhorev\LaravelAmpq\Contracts\ReceiveMessageServiceContract;
use dkhorev\LaravelAmpq\Services\ReceiveMessageService;
use Illuminate\Console\Command;

class ListenAmpqTopicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ampq:listen {connection} {listenStack}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to ampq on {connection} [listenStack]';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $connection = $this->argument('connection');
        $listenStack = $this->argument('listenStack');
        $client = $this->getAmpqClient($connection);

        while (true) {
            /** @var ReceiveMessageServiceContract $service */
            $service = resolve(ReceiveMessageService::class);
            $service->listenStack($client, $listenStack);

            // required for unit testing
            if (app()->environment() !== 'production') {
                return;
            }
        }
    }

    /**
     * @param string $connection
     *
     * @return AmpqClient
     */
    private function getAmpqClient(string $connection): AmpqClient
    {
        $config = config('laravel-ampq.servers.' . $connection);

        return new AmpqClient($config['host'], $config['user'], $config['password'], (int)$config['port']);
    }
}
