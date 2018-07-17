<?php

namespace Tests\utilities;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Console\Application as ConsoleApplication;

trait TogglesExceptionHandling
{
    /**
     * Original application exception handler
     *
     * @var ExceptionHandler|null
     */
    protected $previousExceptionHandler;

    protected function withExceptionHandling()
    {
        if ($this->previousExceptionHandler) {
            $this->app->instance(ExceptionHandler::class, $this->previousExceptionHandler);
        }

        return $this;
    }

    protected function withoutExceptionHandling()
    {
        $this->previousExceptionHandler = app(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new TestCaseDummyHandler);

        return $this;
    }
}

class TestCaseDummyHandler implements ExceptionHandler {
    public function __construct() { }
    public function report(Exception $e) {}
    public function render($request, Exception $e) {
        throw $e;
    }
    public function renderForConsole($output, Exception $e) {
        (new ConsoleApplication)->renderException($e, $output);
    }
}