<?php

namespace App\Exceptions;

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
        // Handle CSRF token mismatch (419 errors) more gracefully
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // If it's a logout request, just redirect to home/login without error
            if ($request->is('admin/logout') || 
                $request->is('admin/auth/logout') || 
                $request->routeIs('logout') ||
                $request->routeIs('filament.admin.auth.logout')) {
                return redirect('/');
            }
            
            // For other requests, redirect to login
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please log in again.');
        });
        
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
