<?php

namespace Tylercd100\LERN;

use Throwable;
use Monolog\Handler\HandlerInterface;
use Tylercd100\LERN\Components\Recorder;
use Tylercd100\LERN\Exceptions\RecorderFailedException;

/**
 * The master class
 */
class LERN
{
    /**
     * @var Throwable
     */
    private $exception;

    /**
     * @var Recorder
     */
    private $recorder;

    /**
     * @param mixed|null $notifier
     * @param Recorder|null $recorder Recorder instance
     */
    public function __construct($notifier = null, Recorder $recorder = null)
    {
        $this->recorder = $this->buildRecorder($recorder);
    }

    /**
     * Will execute record and notify methods
     * @param  Throwable $e   The exception to use
     * @return ExceptionModel the recorded Eloquent Model
     */
    public function handle(Throwable $e)
    {
        $this->exception = $e;
        return $this->record($e);
    }

    /**
     * Stores the exception in the database
     * @param  Throwable $e   The exception to use
     * @return \Tylercd100\LERN\Models\ExceptionModel|false The recorded Exception as an Eloquent Model
     */
    public function record(Throwable $e)
    {
        $this->exception = $e;
        return $this->recorder->record($e);
    }

    /**
     * Pushes on another Monolog Handler
     * @param  HandlerInterface $handler The handler instance to add on
     * @return $this
     */
    public function pushHandler(HandlerInterface $handler)
    {
        return $this;
    }

    /**
     * Get Recorder
     * @return \Tylercd100\LERN\Components\Recorder
     */
    public function getRecorder()
    {
        return $this->recorder;
    }

    /**
     * Set Recorder
     * @param \Tylercd100\LERN\Components\Recorder $recorder A Recorder instance to use
     * @return \Tylercd100\LERN\LERN
     */
    public function setRecorder(Recorder $recorder)
    {
        $this->recorder = $recorder;
        return $this;
    }

    /**
     * Get the log level
     * @return mixed
     */
    public function getLogLevel()
    {
        return null;
    }

    /**
     * Set the log level
     * @param string $level The log level
     * @return \Tylercd100\LERN\LERN
     */
    public function setLogLevel($level)
    {
        return $this;
    }

    /**
     * Constructs a Recorder
     *
     * @param Recorder $recorder
     * @return Recorder
     */
    protected function buildRecorder(Recorder $recorder = null)
    {
        $class = config('lern.record.class');
        $class = !empty($class) ? $class : Recorder::class;
        if (empty($recorder)) {
            $recorder = new $class();
        }
        if ($recorder instanceof Recorder) {
            return $recorder;
        } else {
            throw new RecorderFailedException("LERN was expecting an instance of " . Recorder::class);
        }
    }
}
