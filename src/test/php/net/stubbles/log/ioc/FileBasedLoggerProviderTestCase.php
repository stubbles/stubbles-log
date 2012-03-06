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
 * Test for net\stubbles\log\ioc\FileBasedLoggerProvider.
 *
 * @since  2.0.0
 * @group  log
 * @group  log_ioc
 */
class FileBasedLoggerProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FileBasedLoggerProvider
     */
    private $fileBasedLoggerProvider;
    /**
     * mocked logger provider
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLoggerProvider;
    /**
     * mocked logger
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockLogger              = $this->getMock('net\\stubbles\\log\\Logger', array(), array(), '', false);
        $this->mockLoggerProvider      = $this->getMock('net\\stubbles\\log\\ioc\\LoggerProvider', array(), array(), '', false);
        $this->fileBasedLoggerProvider = new FileBasedLoggerProvider($this->mockLoggerProvider, __DIR__);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->fileBasedLoggerProvider->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.log.path',
                            $refParams[1]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @group  issue_1
     */
    public function annotationsPresentOnSetFileModeMethod()
    {
        $setFileModeMethod = $this->fileBasedLoggerProvider->getClass()
                                                           ->getMethod('setFileMode');
        $this->assertTrue($setFileModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setFileModeMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setFileModeMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.log.filemode',
                            $setFileModeMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function doesNotAppendFileLogAppenderIfLoggerAlreadyHasAppenders()
    {
        $this->mockLoggerProvider->expects($this->once())
                                 ->method('get')
                                 ->will($this->returnValue($this->mockLogger));
        $this->mockLogger->expects($this->once())
                         ->method('hasLogAppenders')
                         ->will($this->returnValue(true));
        $this->mockLogger->expects($this->never())
                         ->method('addLogAppender');
        $this->fileBasedLoggerProvider->get();
    }

    /**
     * @test
     */
    public function appendFileLogAppenderIfLoggerHasNoAppenders()
    {
        $this->mockLoggerProvider->expects($this->once())
                                 ->method('get')
                                 ->with($this->equalTo('foo'))
                                 ->will($this->returnValue($this->mockLogger));
        $this->mockLogger->expects($this->once())
                         ->method('hasLogAppenders')
                         ->will($this->returnValue(false));
        $this->mockLogger->expects($this->once())
                         ->method('addLogAppender')
                         ->with($this->isInstanceOf('net\\stubbles\\log\\appender\\FileLogAppender'))
                         ->will($this->returnArgument(0));
        $this->fileBasedLoggerProvider->get('foo');
    }

    /**
     * @test
     */
    public function appendFileLogAppenderWithDifferentFileMode()
    {
        $this->mockLoggerProvider->expects($this->once())
                                 ->method('get')
                                 ->with($this->equalTo('foo'))
                                 ->will($this->returnValue($this->mockLogger));
        $this->mockLogger->expects($this->once())
                         ->method('hasLogAppenders')
                         ->will($this->returnValue(false));
        $this->mockLogger->expects($this->once())
                         ->method('addLogAppender')
                         ->with($this->isInstanceOf('net\\stubbles\\log\\appender\\FileLogAppender'))
                         ->will($this->returnArgument(0));
        $this->fileBasedLoggerProvider->setFileMode(0777)
                                      ->get('foo');
    }
}
?>