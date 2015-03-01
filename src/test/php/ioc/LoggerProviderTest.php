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
use stubbles\lang\reflect;
use stubbles\log\LogEntry;
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
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogEntryFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockLogEntryFactory = $this->getMock('stubbles\log\entryfactory\LogEntryFactory');
        $this->loggerProvider      = new LoggerProvider($this->mockLogEntryFactory);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue(
                reflect\constructorAnnotationsOf($this->loggerProvider)
                ->contain('Inject')
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
