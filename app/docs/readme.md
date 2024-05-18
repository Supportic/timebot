# Timebot

## Workflow

1. Fetch data from API

## 3rd Party Packages

Holidays: https://www.yasumi.dev/docs/getting-started/
Google API: https://github.com/googleapis/google-api-php-client

## Personio API

https://api.personio.de

### Authentication

In order to talk to the personio API it requires an auth token in each request which can be obtained from the `POST /auth` route.  
This route requires a `client_id` and `client_secret` which can be generated in the personio backend. (see [Settings > API credentials](https://developer.personio.de/docs/getting-started-with-the-personio-api#2-api-access-and-authorization))  
The credentials should have access to:

- employees:read
- attendances:read
- absences:read

You can test your credentials on this site: https://developer.personio.de/reference/post_auth  
The token is valid for 24h.

List all cache pools (from config/packages/cache.yaml):  
`php bin/console cache:pool:list`

Delete cached auth token:  
`php bin/console cache:pool:delete personio.auth.cache personio.api.auth_token`

## Performance

In order to track performance of certain functions we use the [symfony/stopwatch](https://symfony.com/components/Stopwatch) component. It also works with Twig templates.

The start(), stop() and getEvent() methods return a StopwatchEvent object that provides information about the current event.

https://symfony.com/doc/6.4/performance.html#profiling-with-symfony-stopwatch

```php
// the argument is the name of the "profiling event"
$this->stopwatch->start('export-data', 'exporter');

// ...do heavy work...

// reset the stopwatch to delete all the data measured so far
// $this->stopwatch->reset();

$evt = $this->stopwatch->stop('export-data', 'exporter');

dump($evt->__toString());
dump((string) $evt);
dump((string) $this->stopwatch->getEvent('export-data'));
```

### fetching the API

We don't serialize API responses into Models because serializing an arbitrary amount of data into classes creates an overhead of several seconds. (depending on the amount of data)
