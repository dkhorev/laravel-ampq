# Laravel AMPQ helper

Pub/Sub with RabbitMQ in Laravel.

### Installation

`composer require dkhorev/laravel-ampq`

### Publish configs

`php artisan vendor:publish --tag=laravel-ampq --force`

### Listeners

setup your listeners in `laravel-ampq.cfg`

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

```php
use dkhorev\LaravelAmpq\Contracts\AmpqListenCallbackContract;
use PhpAmqpLib\Message\AMQPMessage;

class MyTopicCallback implements AmpqListenCallbackContract
{
    public function __invoke(AMQPMessage $msg): void
    {
        // TODO: Implement __invoke() method.
    }
}
```

it is recommended to only do basic validation of messages in a callback, and queue any heavy processing jobs (to redis or whatever)

### Listen command

`php artisan ampq:listen {connection} {listenStack}`

`connection` - connection name from config's `servers` array

`listenStack` - stack of listening channels from config's `callbacks` array`

### Sending messages

To send a message with this package, you can use any connection from `laravel-ampq.cfg`

#### Using "local" connection (example)

```php
use dkhorev\LaravelAmpq\Clients\AmpqClient;
use dkhorev\LaravelAmpq\Contracts\SendToTopicServiceContract;

$config = config('laravel-ampq.servers.local');

$client =  new AmpqClient($config['host'], $config['user'], $config['password'], (int)$config['port']);
$sender = resolve(SendToTopicServiceContract::class);
$sender->postTo($client, 'some-exchange', 'some-topic', ['exapmple-data' => 'hello world!']);
```

#### Creating custom client connections

add custom server to config

```
'servers' => [
    'custom' => [
    ...
    ],
],
```

extend `AmpqClient` from this package and create an interface, i.e. `AmpqClientCustomInterface`

```php
use dkhorev\LaravelAmpq\Clients\AmpqClient;

class AmpqClientCustom extends AmpqClient implements AmpqClientCustomInterface
{}
```

in your app's `AppServiceProvider` bind new interface `AmpqClientCustomInterface` to resolve with custom server

```php
$this->app->bind(
    AmpqClientCustomInterface::class,
    static function () {
        $config = config('laravel-ampq.servers.custom');

        return new AmpqClientCustom($config['host'], $config['user'], $config['password'], $config['port']);
    }
);
```

### Sending messages

```php
use dkhorev\LaravelAmpq\Contracts\SendToTopicServiceContract;

$sender = resolve(SendToTopicServiceContract::class);
$client = resolve(AmpqClientCustomInterface::class);

$sender->postTo($client, 'some-exchange', 'some-topic', ['exapmple-data' => 'hello world!']);
```
