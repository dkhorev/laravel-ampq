<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Contracts;

interface ReceiveMessageServiceContract
{
    /**
     * Listen data from all topic/exchange on stack
     *
     * @param AmpqClientContract $client
     * @param string             $listenStack
     */
    public function listenStack(AmpqClientContract $client, string $listenStack): void;
}
