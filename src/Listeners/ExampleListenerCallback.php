<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Listeners;

use dkhorev\LaravelAmpq\Contracts\AmpqListenCallbackContract;
use PhpAmqpLib\Message\AMQPMessage;

class ExampleListenerCallback implements AmpqListenCallbackContract
{
    public function __invoke(AMQPMessage $msg): void
    {
        // TODO: Implement __invoke() method.
    }
}
