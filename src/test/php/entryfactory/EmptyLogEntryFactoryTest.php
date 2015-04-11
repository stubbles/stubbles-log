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
 * Test for stubbles\log\entryfactory\EmptyLogEntryFactory.
 *
 * @group  entryfactory
 */
class EmptyLogEntryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  EmptyLogEntryFactory
     */
    private $emptyLogEntryFactory;
    /**
     * created instance
     *
     * @type  LogEntry
     */
    private $logEntry;
    /**
     * mocked logger instance
     *
     * @type  bovigo\callmap\Proxy
     */
    private $logger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->logger               = NewInstance::stub('stubbles\log\Logger');
        $this->emptyLogEntryFactory = new EmptyLogEntryFactory();
        $this->logEntry             = $this->emptyLogEntryFactory->create(
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
    public function createdLogEntryIsEmpty()
    {
        assertEquals('', $this->logEntry);
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
                $this->emptyLogEntryFactory->recreate(
                        $this->logEntry,
                        $this->logger
                )
        );
    }
}
