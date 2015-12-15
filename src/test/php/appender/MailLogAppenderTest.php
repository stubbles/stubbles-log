<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\appender;
use bovigo\callmap\NewInstance;
use stubbles\log\LogEntry;
use stubbles\log\Logger;

use function bovigo\callmap\verify;
/**
 * Test for stubbles\log\appender\MailLogAppender.
 *
 * @group  appender
 */
class MailLogAppenderTest extends \PHPUnit_Framework_TestCase
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
        $this->mailLogAppender = NewInstance::of(
                MailLogAppender::class,
                ['test@example.org']
        )->mapCalls(['sendMail' => true]);
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
        return new LogEntry($target, NewInstance::stub(Logger::class));
    }

    /**
     * @test
     */
    public function finalizeWithoutLogEntriesDoesNotSendMail()
    {
        $this->mailLogAppender->finalize();
        verify($this->mailLogAppender, 'sendMail')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function finalizeWithLogEntriesInNonHostEnvironment()
    {
        $this->tearDown();
        $this->mailLogAppender
                ->append($this->logEntry1->addData('bar')->addData('baz'))
                ->append($this->logEntry2->addData('shit')->addData('happens'))
                ->finalize();
        verify($this->mailLogAppender, 'sendMail')
                ->received(
                        'Debug message from ' . php_uname('n'),
                        "foo: bar|baz\n\nblub: shit|happens\n\n"
                );
    }

    /**
     * @test
     */
    public function finalizeWithLogEntriesInHostEnvironmentWithoutReferer()
    {
        unset($_SERVER['HTTP_REFERER']);
        $this->mailLogAppender
                ->append($this->logEntry1->addData('bar')->addData('baz'))
                ->append($this->logEntry2->addData('shit')->addData('happens'))
                ->finalize();
        verify($this->mailLogAppender, 'sendMail')
                ->received(
                        'Debug message from ' . php_uname('n'),
                        "foo: bar|baz\n\nblub: shit|happens\n\n\nURL that caused this:\nhttp://example.org/example.php?example=dummy\n"
                );
    }

    /**
     * @test
     */
    public function finalizeWithLogEntriesInHostEnvironmentWithReferer()
    {
        $_SERVER['HTTP_REFERER'] = 'referer';
        $this->mailLogAppender
                ->append($this->logEntry1->addData('bar')->addData('baz'))
                ->append($this->logEntry2->addData('shit')->addData('happens'))
                ->finalize();
        verify($this->mailLogAppender, 'sendMail')
                ->received(
                        'Debug message from ' . php_uname('n'),
                        "foo: bar|baz\n\nblub: shit|happens\n\n\nURL that caused this:\nhttp://example.org/example.php?example=dummy\n\nReferer:\nreferer\n"
                );
    }
}
