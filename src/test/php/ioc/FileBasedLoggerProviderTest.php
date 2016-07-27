<?php
declare(strict_types=1);
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
use stubbles\log\Logger;
use stubbles\log\appender\LogAppender;

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
use function stubbles\reflect\annotationsOfConstructorParameter;
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
        $this->logger                  = NewInstance::stub(Logger::class);
        $this->loggerProvider          = NewInstance::stub(LoggerProvider::class);
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
        $logPathParamAnnotations = annotationsOfConstructorParameter(
                'logPath',
                $this->fileBasedLoggerProvider
        );
        assertTrue($logPathParamAnnotations->contain('Named'));
        assert(
                $logPathParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.log.path')
        );

        $fileModeParamAnnotations = annotationsOfConstructorParameter(
                'fileMode',
                $this->fileBasedLoggerProvider
        );
        assertTrue($fileModeParamAnnotations->contain('Named'));
        assert(
                $fileModeParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.log.filemode')
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
        verify($this->logger, 'addAppender')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function appendFileLogAppenderIfLoggerHasNoAppenders()
    {
        $this->loggerProvider->mapCalls(['get' => $this->logger]);
        $this->logger->mapCalls([
                    'hasLogAppenders' => false,
                    'addAppender'     => function(LogAppender $appender) { return $appender; }
        ]);
        $this->fileBasedLoggerProvider->get('foo');
        verify($this->logger, 'addAppender')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function appendFileLogAppenderWithDifferentFileMode()
    {
        $this->loggerProvider->mapCalls(['get' => $this->logger]);
        $this->logger->mapCalls([
                'hasLogAppenders' => false,
                'addAppender'     => function(LogAppender $appender) { return $appender; }
        ]);
        $fileBasedLoggerProvider = new FileBasedLoggerProvider($this->loggerProvider, __DIR__, 0777);
        $fileBasedLoggerProvider->get('foo');
        verify($this->logger, 'addAppender')->wasCalledOnce();
    }
}
