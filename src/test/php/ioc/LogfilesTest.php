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
use stubbles\ioc\Binder;
use stubbles\log\Logger;
use stubbles\log\entryfactory\EmptyLogEntryFactory;
use stubbles\log\entryfactory\LogEntryFactory;
use stubbles\log\entryfactory\TimedLogEntryFactory;
/**
 * Test for stubbles\log\ioc\Logfiles.
 *
 * @group  ioc
 */
class LogfilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * configures the bindings
     *
     * @param   \stubbles\log\ioc\Logfiles  $logfiles
     * @return  \stubbles\ioc\Injector
     */
    private function configureBindings(Logfiles $logfiles)
    {
        $binder = new Binder();
        $logfiles->configure($binder);
        return $binder->getInjector();
    }

    /**
     * @test
     */
    public function logPathIsIsNotBoundWhenNotGiven()
    {
        assertFalse(
                $this->configureBindings(new Logfiles())
                        ->hasConstant('stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logPathIsBoundWhenGiven()
    {
        assertSame(
                __DIR__,
                $this->configureBindings((new Logfiles())->writeTo(__DIR__))
                        ->getConstant('stubbles.log.path')
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryIsBoundAsSingleton()
    {
        $injector = $this->configureBindings(new Logfiles());
        assertSame(
                $injector->getInstance(LogEntryFactory::class),
                $injector->getInstance(LogEntryFactory::class)
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToTimedLogEntryFactoryByDefault()
    {
        assertInstanceOf(
                TimedLogEntryFactory::class,
                $this->configureBindings(new Logfiles())
                        ->getInstance(LogEntryFactory::class)
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToConfiguredLogEntryFactoryClass()
    {
        assertInstanceOf(
                EmptyLogEntryFactory::class,
                $this->configureBindings((new Logfiles())
                                ->createEntriesWith(EmptyLogEntryFactory::class)
                        )->getInstance(LogEntryFactory::class)
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreated()
    {
        assertInstanceOf(
               Logger::class,
               $this->configureBindings((new Logfiles())->writeTo(__DIR__))
                    ->getInstance(Logger::class)
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreatedUsingDifferentLoggerProvider()
    {
        assertInstanceOf(
                Logger::class,
                $this->configureBindings(
                        (new Logfiles())
                                ->writeTo(__DIR__)
                                ->loggerProvidedBy(LoggerProvider::class)
                        )->getInstance(Logger::class)
        );
    }
}
