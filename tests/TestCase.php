<?php

namespace Tests;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Tests\utilities\CreatesMembers;
use Tests\utilities\CreatesStudents;
use Tests\utilities\TogglesExceptionHandling;
use PHPUnit\Framework\Assert as PHPUnit;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TogglesExceptionHandling, CreatesMembers, CreatesStudents;

    /**
     * Setup Test case.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    /**
     * Creates a request with the session data included.
     *
     * @param null $data
     * @return \Illuminate\Http\Request
     */
    protected function newRequestWithSession($data = null)
    {
        if (is_array($data)) $this->withSession($data);

        $request = new \Illuminate\Http\Request();
        $request->setLaravelSession($this->app['session']->driver());

        return $request;
    }

    /**
     * Create a request that accepts json as response.
     *
     * @return \Illuminate\Http\Request
     */
    protected function newJsonRequest()
    {
        $server = $this->transformHeadersToServerVars(['Accept' => 'application/json']);

        return new \Illuminate\Http\Request([], [], [], [], [], $server);
    }

    /**
     * Follows a redirect response.
     *
     * @param RedirectResponse|Response $response
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function follow($response)
    {
        return $this->get($response->headers->get('location'));
    }

    /**
     * Assert that it has flashed a message.
     *
     * @param $message
     * @return $this
     */
    public function assertFlashHas($message)
    {
        // Testing first message only! Might change in the future.
        PHPUnit::assertContains($message, app('session.store')->get('flash_notification.0.message') ?: '',
            'Flash message not found: '.$message);

        return $this;
    }

    /**
     * Asserts that the closure throws validation exception.
     * Checks if invalid field was present in the validation message.
     *
     * @param Closure $closure
     * @param null $expectedInvalid
     */
    public function assertValidationFails(Closure $closure, $expectedInvalid = null) {
        try {
            $failed = false;
            $closure();
        } catch (ValidationException $exception) {
            $failed = true;
            if ($expectedInvalid)
                $this->assertArrayHasKey($expectedInvalid, $exception->validator->getMessageBag()->toArray(),
                    "Validation failed, but expected field '$expectedInvalid' was not present.");
        }
        $this->assertTrue($failed, "Validation did not failed.");
    }
}

