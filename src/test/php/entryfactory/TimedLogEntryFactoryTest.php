<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\log
 */
namespace stubbles\log\entryfactory;
use bovigo\callmap\NewInstance;
/**
 * Test for stubbles\log\entryfactory\TimedLogEntryFactory.
 *
 * @group  entryfactory
 */
class TimedLogEntryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TimedLogEntryFactory
     */
    private $timedLogEntryFactory;
    /**
     * created instance without session
     *
     * @type  LogEntry
     */
    private $logEntry;
    /**
     * mocked logger instance
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $logger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->logger           = NewInstance::stub('stubbles\log\Logger');
        $this->timedLogEntryFactory = new TimedLogEntryFactory();
        $this->logEntry             = $this->timedLogEntryFactory->create(
                'testTarget',
                $this->logger
        );
    }

    /**
     * @test
     */
    public function createdLogEntryHasCorrectTarget()
    {
        assertEquals('testTarget', $this->logEntry->target());
    }

    /**
     * @test
     */
    public function createdLogEntryContainsTime()
    {
        $currentTime = time();
        $loggedTime  = strtotime($this->logEntry);
        assertGreaterThan($currentTime -2, $loggedTime);
        assertLessThan($currentTime +2, $loggedTime);
    }

    /**
     * @test
     */
    public function createdLogEntryCallsGivenLogger()
    {
        $this->logEntry->log();
        assertEquals(
                [$this->logEntry],
                $this->logger->argumentsReceivedFor('log')
        );
    }

    /**
     * @test
     */
    public function recreateOnlyReturnsGivenLogEntryUnmodified()
    {
        assertSame(
                $this->logEntry,
                $this->timedLogEntryFactory->recreate(
                        $this->logEntry,
                        $this->logger
                )
        );
    }
}
