# Laravel AMPQ helper

Pub/Sub with RabbitMQ in Laravel.

### Publish configs

php artisan vendor:publish --tag=laravel-ampq --force

### Listeners

setup our listeners in `laravel-ampq.cfg`

```
'callbacks' => [
    'example_stack' => [
        'exchange' => [
            'topic' => ExampleListenerCallback::class,
        ],
    ],
],
```

this will listen for message on "exchange:topic" and pass any messages to `ExampleListenerCallback->__invoke()` method

every listener should implement `AmpqListenCallbackContract` (__invoke() method)

it is recommended to only do basic validation of messages in a callback, and queue any heavy processing jobs

### Listen command

`php artisan ampq:listen {connection} {listenStack}`

`connection` - connection name from config's `servers` array

`listenStack` - stack of listening channels from config's `callbacks` array`

### Making client connections to send messages

add custom server to config

```
'servers' => [
    'custom' => [
        ...
    ],
],
```

extend `AmpqClient` from this package and create an interface
```
use dkhorev\LaravelAmpq\Clients\AmpqClient;

class AmpqClientCustom extends AmpqClient implements AmpqClientCustomContract
{}
```

then bind new interface `AmpqClientCustomContract` to resolve with custom server

```
$this->app->bind(
    AmpqClientCustomContract::class,
    static function () {
        $config = config('laravel-ampq.servers.custom');

        return new AmpqClientLocal($config['host'], $config['user'], $config['password'], $config['port']);
    }
);
```

typehint `AmpqClientLocalContract` in your service to get this ampq connection

### Send message to topic
to send message
```
$sender = app(SendToTopicServiceContract::class);
``` 

```
$sender->postTo(_AmpqClientContract_ $client, string $exchange, string $topic, array $data);
``
