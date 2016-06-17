5.0.0 (2016-06-17)
------------------

### BC breaks

  * Raised minimum required PHP version to 5.6
  * IoC features are now optional and require stubbles/ioc


### Other changes

    * removed dependency to stubbles/core
    * `stubbles\log\LogEntry::addData()` now accepts arbitrary amounts arguments


4.2.0 (2015-05-28)
------------------

### BC breaks

  * removed `stubbles\log\Logger::addLogAppender()`, use `stubbles\log\Logger::addAppender()` instead, was deprecated since 3.0.0
  * removed `stubbles\log\Logger::getLogAppenders()`, was deprecated since 3.0.0
  * removed `stubbles\log\LogEntry::getTarget()`, use `stubbles\log\LogEntry::target()` instead, was deprecated since 3.0.0
  * removed `stubbles\log\LogEntry::getData()`, use `stubbles\log\LogEntry::data()` instead, was deprecated since 3.0.0
  * removed `stubbles\log\LogEntry::get()`, use `stubbles\log\LogEntry::__toString()` instead, was deprecated since 3.0.0

### Other changes

  * upgraded stubbles/core to 6.0.0


4.1.0 (2014-09-29)
------------------

  * upgraded stubbles/core to 5.1.0
  * deprecated `stubbles\log\appender\FileLogAppender::setMode()`, set file mode with constructor instead, will be removed with 5.0.0


4.0.0 (2014-08-17)
------------------

### BC breaks

  * replaced `stubbles\log\ioc\LogBindingModule` with `stubbles\log\ioc\LogFiles`


### Other changes

  * upgraded stubbles/core to 5.0.0


3.0.0 (2014-08-04)
------------------

### BC breaks

  * removed namespace prefix `net`, base namespace is now `stubbles\log` only
  * deprecated `stubbles\log\Logger::addLogAppender()`, use `stubbles\log\Logger::addAppender()` instead, will be removed with 4.0.0
  * deprecated `stubbles\log\Logger::getLogAppenders()`, will be removed with 4.0.0
  * deprecated `stubbles\log\LogEntry::getTarget()`, use `stubbles\log\LogEntry::target()` instead, will be removed with 4.0.0
  * deprecated `stubbles\log\LogEntry::getData()`, use `stubbles\log\LogEntry::data()` instead, will be removed with 4.0.0
  * deprecated `stubbles\log\LogEntry::get()`, use `stubbles\log\LogEntry::__toString()` instead, will be removed with 4.0.0

### Other changes

  * upgraded to stubbles/core 4.x


2.2.0 (2013-05-02)
------------------

  * upgraded stubbles/core to ~3.0


2.1.0 (2012-07-31)
------------------

  * changed stubbles-core to 2.*


2.0.0 (2012-07-10)
------------------

  * Initial release.
