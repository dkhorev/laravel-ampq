<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Contracts;

use PhpAmqpLib\Connection\AMQPStreamConnection;

interface AmpqClientContract
{
    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection;
}
