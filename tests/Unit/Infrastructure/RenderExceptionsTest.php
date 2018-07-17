<?php

namespace Tests\Unit;

use App\Exceptions\Handler;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RenderExceptionsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function an_authentication_exception_is_rendered_as_json_when_required()
    {
        $response = (new Handler($this->app))
            ->render($this->newJsonRequest(), new AuthenticationException());

        $this->createTestResponse($response)
            ->assertStatus(401)
            ->assertExactJson([
                'error' => trans('errors.unauthenticated')
            ]);
    }

    /** @test */
    public function an_unauthorized_exception_is_rendered_as_html_page()
    {
        $response = (new Handler($this->app))
            ->render(new \Illuminate\Http\Request(), new AuthorizationException());

        $this->createTestResponse($response)
            ->assertStatus(403)
            ->assertSeeText(trans('errors.forbidden'));
    }

    /** @test */
    public function an_unauthorized_exception_is_rendered_as_json_when_required()
    {
        $response = (new Handler($this->app))
            ->render($this->newJsonRequest(), new AuthorizationException());

        $this->createTestResponse($response)
            ->assertStatus(403)
            ->assertExactJson([
                'error' => trans('errors.unauthorized')
            ]);
    }

    /** @test */
    public function a_model_not_found_is_rendered_as_json_when_required()
    {
        $response = (new Handler($this->app))
            ->render($this->newJsonRequest(), new ModelNotFoundException());

        $this->createTestResponse($response)
            ->assertStatus(404)
            ->assertExactJson([
                'error' => trans('errors.modelnotfound')
            ]);
    }
}
