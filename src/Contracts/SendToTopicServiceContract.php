<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Contracts;

interface SendToTopicServiceContract
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
    public function postTo(AmpqClientContract $client, string $exchange, string $topic, array $data): bool;
}
