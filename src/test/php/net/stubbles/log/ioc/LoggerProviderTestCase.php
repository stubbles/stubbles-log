<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\log
 */
namespace net\stubbles\log\ioc;
use net\stubbles\log\LogEntry;
/**
 * Test for net\stubbles\log\ioc\LoggerProvider.
 *
 * @group  ioc
 */
class LoggerProviderTestCase extends \PHPUnit_Framework_TestCase
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
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogEntryFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockLogEntryFactory = $this->getMock('net\\stubbles\\log\\entryfactory\\LogEntryFactory');
        $this->loggerProvider      = new LoggerProvider($this->mockLogEntryFactory);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->loggerProvider->getClass()
                                               ->getConstructor()
                                               ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function createdLoggerUsesGivenLogEntryFactory()
    {
        $logger   = $this->loggerProvider->get();
        $logEntry = new LogEntry('testTarget', $logger);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('create')
                                  ->will($this->returnValue($logEntry));
        $this->assertSame($logEntry, $logger->createLogEntry('testTarget'));
    }

    /**
     * @test
     */
    public function createsDifferentInstancesForDifferentNames()
    {
        $this->assertNotSame($this->loggerProvider->get(),
                             $this->loggerProvider->get('foo')
        );
    }

    /**
     * @test
     */
    public function returnsSameInstanceForSameName()
    {
        $this->assertSame($this->loggerProvider->get(),
                          $this->loggerProvider->get()
        );
    }
}
?>