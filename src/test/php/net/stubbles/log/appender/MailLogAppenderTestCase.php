<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\appender;
use net\stubbles\log\LogEntry;
/**
 * Test for net\stubbles\log\appender\MailLogAppender.
 *
 * @group  appender
 */
class MailLogAppenderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MailLogAppender
     */
    private $mailLogAppender;
    /**
     * log entry instance
     *
     * @type  LogEntry
     */
    private $logEntry1;
    /**
     * log entry instance
     *
     * @type  LogEntry
     */
    private $logEntry2;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mailLogAppender   = $this->getMock('net\\stubbles\\log\\appender\\MailLogAppender',
                                                  array('sendMail'),
                                                  array('test@example.org')
                                   );
        $_SERVER['HTTP_HOST']    = 'example.org';
        $_SERVER['PHP_SELF']     = '/example.php';
        $_SERVER['QUERY_STRING'] = 'example=dummy';
        $this->logEntry1         = $this->createLogEntry('foo');
        $this->logEntry2         = $this->createLogEntry('blub');
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['PHP_SELF']);
        unset($_SERVER['QUERY_STRING']);
        unset($_SERVER['HTTP_REFERER']);
    }

    /**
     * creates log entry
     *
     * @return  stubLogEntry
     */
    protected function createLogEntry($target)
    {
        return new LogEntry($target,
                            $this->getMock('net\\stubbles\\log\\Logger',
                                        array(),
                                        array(),
                                        '',
                                        false
                            )
        );
    }

    /**
     * @test
     */
    public function finalizeWithoutLogEntriesDoesNotSendMail()
    {
        $this->mailLogAppender->expects($this->never())
                              ->method('sendMail');
        $this->mailLogAppender->finalize();
    }

    /**
     * @test
     */
    public function finalizeWithLogEntriesInNonHostEnvironment()
    {
        $this->tearDown();
        $this->mailLogAppender->expects($this->once())
                              ->method('sendMail')
                              ->with($this->equalTo('Debug message from ' . php_uname('n')),
                                     $this->equalTo("foo: bar|baz\n\nblub: shit|happens\n\n")
                                );
        $this->mailLogAppender->append($this->logEntry1->addData('bar')
                                                       ->addData('baz')
                                )
                              ->append($this->logEntry2->addData('shit')
                                                       ->addData('happens')
                                )
                              ->finalize();
    }

    /**
     * @test
     */
    public function finalizeWithLogEntriesInHostEnvironmentWithoutReferer()
    {
        unset($_SERVER['HTTP_REFERER']);
        $this->mailLogAppender->expects($this->once())
                              ->method('sendMail')
                              ->with($this->equalTo('Debug message from ' . php_uname('n')),
                                     $this->equalTo("foo: bar|baz\n\nblub: shit|happens\n\n\nURL that caused this:\nhttp://example.org/example.php?example=dummy\n")
                                );
        $this->mailLogAppender->append($this->logEntry1->addData('bar')
                                                       ->addData('baz')
                                )
                              ->append($this->logEntry2->addData('shit')
                                                       ->addData('happens')
                                )
                              ->finalize();
    }

    /**
     * @test
     */
    public function finalizeWithLogEntriesInHostEnvironmentWithReferer()
    {
        $_SERVER['HTTP_REFERER'] = 'referer';
        $this->mailLogAppender->expects($this->once())
                              ->method('sendMail')
                              ->with($this->equalTo('Debug message from ' . php_uname('n')),
                                     $this->equalTo("foo: bar|baz\n\nblub: shit|happens\n\n\nURL that caused this:\nhttp://example.org/example.php?example=dummy\n\nReferer:\nreferer\n")
                                );
        $this->mailLogAppender->append($this->logEntry1->addData('bar')
                                                       ->addData('baz')
                                )
                              ->append($this->logEntry2->addData('shit')
                                                       ->addData('happens')
                                )
                              ->finalize();
    }
}
?>