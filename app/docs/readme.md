# Timebot

## Workflow

1. Fetch data from API

## 3rd Party Packages

Holidays: https://www.yasumi.dev/docs/getting-started/
Google API: https://github.com/googleapis/google-api-php-client

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

## Email

Testing: `php bin/console mailer:test someone@example.com`
