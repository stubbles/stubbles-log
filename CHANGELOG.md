3.0.0 (2014-07-31)
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
