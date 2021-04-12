<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Contracts;

use PhpAmqpLib\Message\AMQPMessage;

interface AmpqListenCallbackContract
{
    /**
     * @param AMQPMessage $msg
     *
     * @return void
     */
    public function __invoke(AMQPMessage $msg): void;
}
