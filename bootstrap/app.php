<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*'));
        $exceptions->render(function (ValidationException $exception, $request) {
            if ($request->is('api/*')) return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $exception->errors()], 422);
        });
        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->is('api/*')) return response()->json(['success' => false, 'message' => 'Unauthenticated.', 'errors' => []], 401);
        });
        $exceptions->render(function (AuthorizationException $exception, $request) {
            if ($request->is('api/*')) return response()->json(['success' => false, 'message' => 'This action is unauthorized.', 'errors' => []], 403);
        });
    })->create();
