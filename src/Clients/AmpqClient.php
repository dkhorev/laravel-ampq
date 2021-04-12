<?php

declare(strict_types=1);

namespace dkhorev\LaravelAmpq\Clients;

use dkhorev\LaravelAmpq\Contracts\AmpqClientContract;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class AmpqClient
 *
 * @package App\Services\Ampq
 */
class AmpqClient implements AmpqClientContract
{
    private const PORT = 5672;

    protected string $host;
    protected string $login;
    protected string $password;
    protected int $port;

    /**
     * AmpqClient constructor.
     *
     * @param string $host
     * @param string $login
     * @param string $password
     * @param int    $port
     */
    public function __construct(string $host, string $login, string $password, int $port)
    {
        $this->host = $host;
        $this->login = $login;
        $this->password = $password;
        $this->port = $port;
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            $this->host,
            $this->port ?? static::PORT,
            $this->login,
            $this->password
        );
    }
}
