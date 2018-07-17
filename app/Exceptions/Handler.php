<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Debug\Exception\FlattenException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof AuthorizationException)
            return $this->unauthorized($request, $e);

        if ($e instanceof ModelNotFoundException)
            return $this->modelNotFound($request, $e);

        return parent::render($request, $e);
    }

    /**
     * Convert an authorization exception into an unauthorized response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  AuthorizationException  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthorized($request, $e)
    {
        if ($request->expectsJson())
            return response()->jsonError(ucfirst(trans('errors.unauthorized')), 403);

        $httpException = new HttpException(403, $e->getMessage());

        return $this->prepareResponse($request, $httpException);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $e)
    {
        if ($request->expectsJson())
            return response()->jsonError(ucfirst(trans('errors.unauthenticated')), 401);

        $guard = array_get($e->guards(), 0);
        $route = config("auth.guards.$guard.route", 'auth.guards.student.route');

        return redirect()->guest(route($route));
    }

    /**
     * Convert a model not found exception into a response.
     *
     * @param $request
     * @param ModelNotFoundException $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function modelNotFound($request, ModelNotFoundException $e)
    {
        if ($request->expectsJson())
            return response()->jsonError(ucfirst(trans('errors.modelnotfound')), 404);

        $httpException = new NotFoundHttpException($e->getMessage(), $e);

        return $this->prepareResponse($request, $httpException);
    }

    /**
     * @param Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        if (app()->environment('production')) {
            $e = FlattenException::create($e);

            return response()->view('errors.500',
                ['exception' => $e], $e->getStatusCode(), $e->getHeaders());
        }

        return parent::convertExceptionToResponse($e);
    }
}
