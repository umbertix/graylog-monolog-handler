graylog-monolog-handler
===========================

Current Version: 0.1

## Installation

Add the following to your composer.json and run `composer update`

```json
{
    "require": {
        "umbertix/graylog-monolog-handler": "dev-master"
    }
}
```

## Usage

```php
$monolog->pushHandler(new \GraylogMonolog\Handler\GraylogHandler());
```

#### Full example
```php
$monolog = new Logger('TestLog');
$monolog->pushHandler(new \GraylogMonolog\Handler\GraylogHandler());
$monolog->addWarning('This is a warning logging message');
```
