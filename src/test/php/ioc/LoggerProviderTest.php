<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\ioc;
use bovigo\callmap\NewInstance;
use stubbles\log\LogEntry;
use stubbles\log\entryfactory\LogEntryFactory;
/**
 * Test for stubbles\log\ioc\LoggerProvider.
 *
 * @group  ioc
 */
class LoggerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LoggerProvider
     */
    private $loggerProvider;
    /**
     * mocked log entry factory
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $logEntryFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->logEntryFactory = NewInstance::of(LogEntryFactory::class);
        $this->loggerProvider  = new LoggerProvider($this->logEntryFactory);
    }

    /**
     * @test
     */
    public function createdLoggerUsesGivenLogEntryFactory()
    {
        $logger   = $this->loggerProvider->get();
        $logEntry = new LogEntry('testTarget', $logger);
        $this->logEntryFactory->mapCalls(['create' => $logEntry]);
        assertSame($logEntry, $logger->createLogEntry('testTarget'));
    }

    /**
     * @test
     */
    public function createsDifferentInstancesForDifferentNames()
    {
        assertNotSame(
                $this->loggerProvider->get(),
                $this->loggerProvider->get('foo')
        );
    }

    /**
     * @test
     */
    public function returnsSameInstanceForSameName()
    {
        assertSame($this->loggerProvider->get(), $this->loggerProvider->get());
    }
}
