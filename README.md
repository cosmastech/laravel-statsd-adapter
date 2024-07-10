[![Latest Stable Version](http://poser.pugx.org/cosmastech/laravel-statsd-adapter/v)](https://packagist.org/packages/cosmastech/laravel-statsd-adapter) [![Total Downloads](http://poser.pugx.org/cosmastech/laravel-statsd-adapter/downloads)](https://packagist.org/packages/cosmastech/laravel-statsd-adapter) [![License](http://poser.pugx.org/cosmastech/laravel-statsd-adapter/license)](https://packagist.org/packages/cosmastech/laravel-statsd-adapter) [![PHP Version Require](http://poser.pugx.org/cosmastech/laravel-statsd-adapter/require/php)](https://packagist.org/packages/cosmastech/laravel-statsd-adapter)

# Laravel StatsD Adapter

## Overview

The Laravel StatsD Adapter is a package that provides a seamless integration between Laravel applications and StatsD,
a network daemon for collecting and aggregating metrics. By using this adapter, you can effortlessly monitor and 
measure the performance of your Laravel application, track various metrics, and send them to a StatsD server.

## Why Use This Adapter?

- **Save time**: logs are great, but metrics can quickly tell the big picture of your application's health.
- **Performance Monitoring**: Easily track the performance of your Laravel application, including response times, database queries, and other custom metrics.
- **Aggregation**: StatsD collects and aggregates metrics, providing valuable insights into your application's performance over time.
- **Flexibility**: Configure the adapter to suit your specific needs. You can use multiple statsd instances in one environment or configure each environment to write to a different location: your local environment to write to a log, staging to a statsd instance, and production to DataDog.
- **Testability**: use `memory` adapter to write unit tests which confirm that stats are recorded under given conditions.

## Installation

You can install the package via Composer:

```shell
composer require cosmastech/laravel-statsd-adapter
```

After installing the package, publish the configuration file using the following command:

```shell
php artisan vendor:publish --provider="\Cosmastech\LaravelStatsDAdapter\StatsDAdapterServiceProvider"
```

## Configuration

The configuration file `config/statsd-adapter.php` allows you to customize the adapter's behavior. 

Here are the available options:

- **Default Connection**: Specify the default StatsD connection.
- **Default Tags**: In addition to sending tags based on an as needed basis, you can also include tags in every outgoing request.
- **Connections**: Define multiple StatsD connections, each with its own settings.

You can use the example configuration, 

```php
return [
    'default' => env("STATSD_ADAPTER_DEFAULT", "datadog"),

    "default_tags" => [
        "app_version" => "1.0.2",
    ],
    'channels' => [
        "datadog" => [
            "adapter" => "datadog",
            "host" => env("DD_AGENT_HOST"),
            "port" => env("DD_DOGSTATSD_PORT"),
            "socket_path" => null,
            "datadog_host" => null,
            "decimal_precision" => null,
            "global_tags" => [],
            "metric_prefix" => null,
            "disable_telemetry" => null,
        ],
    ],
];
```

## Usage

### Basic Usage

To send a simple metric, you can use the `Stats` facade:

```php
use Cosmastech\LaravelStatsDAdapter\Stats;

// Increment a counter
Stats::increment('page.views');

// Record a gauge
Stats::gauge('user.login', 1);

// Record a timing (in ms)
Stats::timing('response.time', 320);
```

If you prefer using dependency injection in your functions, use the `StatsDClientAdapter` interface.

```php
use Cosmastech\StatsDClientAdapter\Adapters\StatsDClientAdapter;
use App\Models\User;
use App\Models\Post;

class DeleteAllUserPostsAction
{
    public function __construct(private readonly StatsDClientAdapter $statsClient)
    {
    }
    
    public function __invoke(User $user): void
    {
        $user->posts->each(function(Post $post) use ($user) {
            $post->delete();
            $this->statsClient->decrement("posts", 1.0, ["user_id" => $user->id], 1);
        });
    }
}
```

### Use Cases

#### Tracking Page Views

Track the number of times a page is viewed:

```php
Stats::increment('page.views', tags: ['url' => '/home']);
```

#### Monitoring Response Times

Measure and monitor the response time of your application:

```php
function makeSomeApiCall()
{
    return \Http::get("https://packagist.org/packages/list.json?vendor=cosmastech");
}

$apiResponseToDoSomethingWith = Stats::time(makeSomeApiCall(...), "api-request");
```

#### Database Query Monitoring

Track the number of database queries and their execution time:

```php
\DB::listen(function ($query) {
    Stats::increment('database.queries');
    Stats::timing('database.query_time', $query->time);
});
```

## Advanced Configuration

### Custom Connections

You can define multiple connections and use them as needed:

```php
Stats::channel('memory')->increment('custom.metric');
```

### Dynamic Metrics

Create dynamic metric names based on runtime data:

```php
$role = auth()->role; // Let's assume the User model has a property named `role`
Stats::increment("user.{$role}.login");
```

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss your ideas.

## License

This package is open-sourced software licensed under the [WTFPL license](LICENSE.txt).
