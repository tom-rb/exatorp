<?php

namespace Tests\Unit;

use App\Members\Middleware\MustBeGuest;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MustBeGuestTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var TestResponse
     */
    private $response;

    /** @test */
    public function it_lets_guests_through()
    {
        $this->assertTrue($this->tryToPass(new MustBeGuest()));
    }

    /** @test */
    public function it_redirects_when_a_member_is_logged_in()
    {
        $this->signIn();

        $this->assertFalse($this->tryToPass(new MustBeGuest()));

        $this->response->assertRedirect(route('member.home'));
    }

    /** @test */
    public function it_redirects_when_a_student_is_logged_in()
    {
        $this->signInStudent();

        $this->assertFalse($this->tryToPass(new MustBeGuest()));

        $this->response->assertRedirect(route('student.home'));
    }

    private function tryToPass($middleware)
    {
        $passed = false;

        $response = $middleware->handle(new Request(), function($r) use (&$passed) {
            $passed = true;
        });
        $this->response = $this->createTestResponse($response);

        return $passed;
    }
}
