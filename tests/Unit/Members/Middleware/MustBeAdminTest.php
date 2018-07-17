<?php

namespace Tests\Unit;

use App\Members\Middleware\MustBeAdmin;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MustBeAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var TestResponse
     */
    private $response;

    /** @test */
    public function it_lets_admins_through()
    {
        $this->signIn($this->admin());

        $this->assertTrue($this->tryToPass(new MustBeAdmin()));
    }

    /** @test */
    public function it_redirects_home_if_not_an_admin()
    {
        $this->signIn();
        $this->assertFalse($this->tryToPass(new MustBeAdmin()));
        $this->response->assertRedirect(route('member.home'));

        $this->signInStudent();
        $this->assertFalse($this->tryToPass(new MustBeAdmin()));
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
