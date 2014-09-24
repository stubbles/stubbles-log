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
use stubbles\lang;
/**
 * Test for stubbles\log\ioc\FileBasedLoggerProvider.
 *
 * @since  2.0.0
 * @group  log
 * @group  log_ioc
 */
class FileBasedLoggerProviderTest extends \PHPUnit_Framework_TestCase
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
        $this->mockLogger              = $this->getMockBuilder('stubbles\log\Logger')
                                              ->disableOriginalConstructor()
                                              ->getMock();
        $this->mockLoggerProvider      = $this->getMockBuilder('stubbles\log\ioc\LoggerProvider')
                                              ->disableOriginalConstructor()
                                              ->getMock();
        $this->fileBasedLoggerProvider = new FileBasedLoggerProvider($this->mockLoggerProvider, __DIR__);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = lang\reflectConstructor($this->fileBasedLoggerProvider);
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[1]->hasAnnotation('Named'));
        $this->assertEquals(
                'stubbles.log.path',
                $parameters[1]->annotation('Named')->getName()
        );
        $this->assertTrue($parameters[2]->hasAnnotation('Named'));
        $this->assertEquals(
                'stubbles.log.filemode',
                $parameters[2]->annotation('Named')->getName()
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
                         ->method('addAppender');
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
                         ->method('addAppender')
                         ->with($this->isInstanceOf('stubbles\log\appender\FileLogAppender'))
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
                         ->method('addAppender')
                         ->with($this->isInstanceOf('stubbles\log\appender\FileLogAppender'));
        $fileBasedLoggerProvider = new FileBasedLoggerProvider($this->mockLoggerProvider, __DIR__, 0777);
        $fileBasedLoggerProvider->get('foo');
    }
}
