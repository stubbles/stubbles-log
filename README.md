stubbles/log
============

Provides support for writing log data which are business related. For a more
technical approach to logging check PSR-3 compliant packages.


Build status
------------

[![Build Status](https://secure.travis-ci.org/stubbles/stubbles-log.png)](http://travis-ci.org/stubbles/stubbles-log)
[![Coverage Status](https://coveralls.io/repos/stubbles/stubbles-log/badge.png?branch=master)](https://coveralls.io/r/stubbles/stubbles-log?branch=master)

[![Latest Stable Version](https://poser.pugx.org/stubbles/log/version.png)](https://packagist.org/packages/stubbles/log)
[![Latest Unstable Version](https://poser.pugx.org/stubbles/log/v/unstable.png)](//packagist.org/packages/stubbles/log)


Installation
------------

_stubbles/log_ is distributed as [Composer](https://getcomposer.org/)
package. To install it as a dependency of your package use the following
command:

    composer require "stubbles/log": "^5.0"


Requirements
------------

_stubbles/log_ requires at least PHP 5.6.


Usage
-----

In your class where you want to write logs simply create a dependency to
`stubbles\log\Logger` and use this instance to write log data:

```php
namespace example;
use stubbles\log\Logger;
class ExampleClass
{
    private $logger;

    /**
     * @param  Logger  $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function doSomething()
    {
        // maybe some code here that does something
        $this->logger->createLogEntry('myapp')
                ->addData('foo', 'bar', 'baz')
                ->log();
        // maybe some code afterwards that does something
    }
}
```

In the `doSomething()` method we use the logger instance to create a log entry,
and to add data to it. We can do this in one call, or we can add as many calls
to `addData()` as required. After we finished adding all data we want to log we
call the `log()` method which will take care of the actual logging process.

The string passed to `createLogEntry()` is the target where the log entry should
end. What this means in practice depends on the log appender added to the logger.
For instance, the file log appender will use the target to create a log file
with this name, in this case _myapp.log_.

### Delayed logging

Sometimes you want to log data, but not all of the data required to create the
basic log entry is already available. This is where delayed logging comes into
play. Instead of calling `log()` you simple call `logDelayed()`:

```php
namespace example;
use stubbles\log\Logger;
class ExampleClass
{
    protected $logger;

    /**
     * @param  Logger  $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function doSomething()
    {
        // maybe some code here that does something
        $this->logger->createLogEntry('myapp')
                ->addData('foo', 'bar', 'baz')
                ->logDelayed();
        // maybe some code afterwards that does something
    }
}
```

This will cause a delay in logging the data, it will be logged latest when PHP
process is shutdown, so if you are in a web environment this is most likely at
the end of the request. Now all data of the prefilled log entry may be updated
by the log entry factory used.


Log entry factories
-------------------

### Purpose

When new log data is created, you don't want to care about standard log data in
all places where data should be logged. Log entry factories take care of creating
log entries which are prefilled with data. This can be a timestamp, a session id
or any other data you want to have in all log entries. Specific log data
creation then only has to care about adding the specific log data.


### Create log entries with a timestamp

Most common use case for log data is that the first field contains a timestamp
of when this specific log data was created. For this the
`stubbles\log\entryfactory\TimedLogEntryFactory` creates log entries which have
the current timestamp in format _Y-m-d H:i:s_ as their first field.


### Create non-prefilled log entries

Sometimes it is necessary to create non prefilled log entries which after
writing the log data contain the business log data only, e.g. in test cases.
That's when the `stubbles\log\entryfactory\EmptyLogEntryFactory` can be used.


### Custom log entry factories

In case the provided log entry factories don't fulfil your needs you can create
your own log entry factory. For this you have to implement the
`stubbles\log\entryfactory\LogEntryFactory` interface which consists of two
methods:

```php
public function create($target, Logger $logger)
public function recreate(LogEntry $logEntry, Logger $logger)
```

The `create()` method should create new instances of `stubbles\log\LogEntry`.
Both parameters need to be passed to the `stubbles\log\LogEntry` constructor.
After creation, your implementation can add any log data it wants to have in all
log entries. After pre filling the created log entry instance must be returned.

The `recreate()` is called for all log entries which are logged delayed. This is
useful in case during the initial creation of the log entry not all data was
available - now all data in the log entry can be replaced with the most likely
now available data. In case your implementation does not have such a use case it
can be helpful to extend from `stubbles\log\entryfactory\AbstractLogEntryFactory`
which already contains an empty implementation for this method.


Log appenders
-------------

### Purpose

The `stubbles\log\Logger` class itself does not know how the log data is stored.
Storing the log data is the task of a `stubbles\log\appender\LogAppender`. A log
appender takes log data and writes it to the target. The target can be a file, a
database or anything else suited for log data. As a concrete example _stubbles/log_
offers a `stubbles\log\appender\FileLogAppender` which writes the log data into
a logfile on the hard disk.


### File log appender

The file log appender takes the log data and writes it into log files. It is the
default appender used by _stubbles/log_ if no other appender is specified and
the log binding module (see below) is used without any alterations.

It writes the log files to a specific directory. For the default configuration
this means the directory which is specified with the binding constant _stubbles.log.path_.
In case the directory does not exist it is created beforehand. Newly created
directories have the permission _0700_. If you want a different permission you
need to specify a constant binding named _stubbles.log.filemode_ with the
permission value.

The name of the log file will consist of the log entry target with the current
date in the format _YYYY-MM-DD_ and the file ending _.log_ appended. So suppose
today would be February 4 2012 and you create the following log data:

`$logger->create('example')->addData('foo','bar')->log();`

This will create a log file _example-2012-02-04.log_ which has at least the
content _foo|bar_.


### Memory log appender

The memory log appender can be used to test classes which create log data.
Instead of writing the log entries to a file or a database it simply keeps them
in memory so they can be retrieved later on to perform assertions on the written
log data. Suppose we have the following class:

```php
namespace example;
use stubbles\log\Logger;
class ExampleClass
{
    private $logger;

    /**
     * @param  Logger  $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function doSomething()
    {
        ...
        $this->logger->createLogEntry('myapp')
                ->addData('foo', 'bar', 'baz')
                ->log();
        ...
    }
}
```

How can we make sure the correct log data is created, e.g. in a test? The answer
to this is really simple - use the memory log appender:

```php
public function testLogging()
{
    $logger = new Logger(new EmptyLogEntryFactory());
    $memoryLogAppender = new MemoryLogAppender();
    $logger->addLogAppender($memoryLogAppender);
    $exampleClasss = new ExampleClass($logger);
    $exampleClass->doSomething();

    $this->assertEquals(1, $memoryLogAppender->countLogEntries('myapp'));
    $this->assertEquals(
            ['foo', 'bar', 'baz'],
            $memoryLogAppender->getLogEntryData('myapp', 0)
    );
}
```

The memory log appender provides three methods to check for the logged data:

#### `countLogEntries($target)`

Returns the amount of log entries created for the given target.

#### `getLogEntryData($target, $position)`

Returns the logged data for the given target as an array. The position denotes
the order in which log entries for this target were written. If there are two
entries for the target _myapp_ the first can be found at position 0 and the
second at position 1.

#### `getLogEntries($target)`

Returns a list of all `stubbles\log\LogEntry` instances for the given target.


### Custom log appenders

Other log appenders may be created by implementing the `stubbles\log\appender\LogAppender`
interface. This interface consists of two methods:

```php
    append(LogEntry $logEntry);
    finalize();
```

While `append(LogEntry $logEntry)` takes the log entry the log appender can do
with it whatever it wants. The `finalize()` method is called when the PHP process
ends, which means the end of the request in a web environment. The purpose of
the `finalize()` method is to do anything necessary to persist the log entries
which could not be done before.

A log appender can be added to a concrete `stubbles\log\Logger` instance via the
`addLogAppender()` method:

```php
$myLogAppender = new MyLogAppender('/path/to/dir');
$logger->addLogAppender($myLogAppender);
```


Integration with a Stubbles App from _stubbles/ioc_
---------------------------------------------------

Simply add the `stubbles\log\ioc\Logfiles` binding module to the list of bindings
returned by your application class:

```php
namespace example;
use stubbles\App;
class MyApplication extends App
{
    /**
     * returns a list of binding modules used to wire the object graph
     *
     * @return  array
     */
    public static function __bindings()
    {
        return [
                new Logfiles(),
                new StuffRequiredForExampleApplicationBindingModule()
        ];
    }

    // application methods here
}
```

The log binding module provides three ways to further configure how logs should
be created:

 * `createEntriesWith($logEntryFactory)` accepts a class name or instance for a
  log entry factory to be used instead of the default. When not changed, the
  `stubbles\log\entryfactory\TimedLogEntryFactory` is used by default.
 * `loggerProvidedBy($loggerProvider)` changes the default logger instance
   provider to the class or instance given.
 * `writeTo($logPath)` can be used if you want complete freedom to name the log
    path. You can use this method to bind the _stubbles.log.path_ constant to
    any path you like, _/var/log/yourapp_ for example.
