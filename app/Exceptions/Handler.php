<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use RuntimeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Something went wrong.',
                'errors' => ['session' => ['Unauthenticated']]
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof HttpException) {
            return response()->json([
                'message' => 'Something went wrong.',
                'errors' => ['maintenance' => ['System is in maintenance mode.']]
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        if ($exception instanceof RuntimeException) {
            return response()->json([
                'message' => 'Something went wrong.',
                'errors' => ['exception' => [$exception->getMessage()]]
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        return parent::render($request, $exception);
    }
}
