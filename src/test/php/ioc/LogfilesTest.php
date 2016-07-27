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
use stubbles\ioc\Binder;
use stubbles\ioc\Injector;
use stubbles\log\Logger;
use stubbles\log\entryfactory\EmptyLogEntryFactory;
use stubbles\log\entryfactory\LogEntryFactory;
use stubbles\log\entryfactory\TimedLogEntryFactory;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\log\ioc\Logfiles.
 *
 * @group  ioc
 */
class LogfilesTest extends \PHPUnit_Framework_TestCase
{
    private function configureBindings(Logfiles $logfiles): Injector
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
        assert(
                $this->configureBindings((new Logfiles())->writeTo(__DIR__))
                        ->getConstant('stubbles.log.path'),
                equals(__DIR__)
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryIsBoundAsSingleton()
    {
        $injector = $this->configureBindings(new Logfiles());
        assert(
                $injector->getInstance(LogEntryFactory::class),
                isSameAs($injector->getInstance(LogEntryFactory::class))
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToTimedLogEntryFactoryByDefault()
    {
        assert(
                $this->configureBindings(new Logfiles())
                        ->getInstance(LogEntryFactory::class),
                isInstanceOf(TimedLogEntryFactory::class)
        );
    }

    /**
     * @test
     */
    public function logEntryFactoryClassIsBoundToConfiguredLogEntryFactoryClass()
    {
        assert(
                $this->configureBindings((new Logfiles())
                                ->createEntriesWith(EmptyLogEntryFactory::class)
                        )->getInstance(LogEntryFactory::class),
                isInstanceOf(EmptyLogEntryFactory::class)
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreated()
    {
        assert(
               $this->configureBindings((new Logfiles())->writeTo(__DIR__))
                    ->getInstance(Logger::class),
            isInstanceOf(Logger::class)
        );
    }

    /**
     * @test
     */
    public function loggerCanBeCreatedUsingDifferentLoggerProvider()
    {
        assert(
                $this->configureBindings(
                        (new Logfiles())
                                ->writeTo(__DIR__)
                                ->loggerProvidedBy(LoggerProvider::class)
                        )->getInstance(Logger::class),
                isInstanceOf(Logger::class)
        );
    }
}
