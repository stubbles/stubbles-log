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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
use stubbles\lang\reflect;
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
     * @type  \bovigo\callmap\Proxy
     */
    private $loggerProvider;
    /**
     * mocked logger
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $logger;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->logger                  = NewInstance::stub('stubbles\log\Logger');
        $this->loggerProvider          = NewInstance::stub('stubbles\log\ioc\LoggerProvider');
        $this->fileBasedLoggerProvider = new FileBasedLoggerProvider(
                $this->loggerProvider,
                __DIR__
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $logPathParamAnnotations = reflect\annotationsOfConstructorParameter(
                'logPath',
                $this->fileBasedLoggerProvider
        );
        assertTrue($logPathParamAnnotations->contain('Named'));
        assertEquals(
                'stubbles.log.path',
                $logPathParamAnnotations->firstNamed('Named')->getName()
        );

        $fileModeParamAnnotations = reflect\annotationsOfConstructorParameter(
                'fileMode',
                $this->fileBasedLoggerProvider
        );
        assertTrue($fileModeParamAnnotations->contain('Named'));
        assertEquals(
                'stubbles.log.filemode',
                $fileModeParamAnnotations->firstNamed('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function doesNotAppendFileLogAppenderIfLoggerAlreadyHasAppenders()
    {
        $this->loggerProvider->mapCalls(['get' => $this->logger]);
        $this->logger->mapCalls(['hasLogAppenders' => true]);
        $this->fileBasedLoggerProvider->get();
        callmap\verify($this->logger, 'addAppender')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function appendFileLogAppenderIfLoggerHasNoAppenders()
    {
        $this->loggerProvider->mapCalls(['get' => $this->logger]);
        $this->logger->mapCalls(['hasLogAppenders' => false]);
        $this->fileBasedLoggerProvider->get('foo');
        callmap\verify($this->logger, 'addAppender')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function appendFileLogAppenderWithDifferentFileMode()
    {
        $this->loggerProvider->mapCalls(['get' => $this->logger]);
        $this->logger->mapCalls(['hasLogAppenders' => false]);
        $fileBasedLoggerProvider = new FileBasedLoggerProvider($this->loggerProvider, __DIR__, 0777);
        $fileBasedLoggerProvider->get('foo');
        callmap\verify($this->logger, 'addAppender')->wasCalledOnce();
    }
}
