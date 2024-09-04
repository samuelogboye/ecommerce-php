<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Return a JSON response with a 401 status code
        return response()->json(['error' => 'Unauthenticated access'], 401);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Check for ThrottleRequestsException and return a JSON response
        if ($exception instanceof ThrottleRequestsException) {
            \Log::info('Expect JSON:', [$request->expectsJson()]);
            dd($request->expectsJson());
            return response()->json([
                'error' => 'Too Many Requests',
            ], 429);
        }

        return parent::render($request, $exception);
    }
}
