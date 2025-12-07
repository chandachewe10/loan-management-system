<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompleteness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First check if user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Allow access to profile completion page and other essential routes
        if ($this->shouldAllowAccess($request)) {
            return $next($request);
        }

        // Don't check profile completeness if email is not verified yet
        // Let email verification handle the redirect first
        if (!$user->hasVerifiedEmail()) {
            return $next($request);
        }

        // Check if profile is incomplete - redirect only if modal hasn't been shown (user hasn't clicked "Complete Later")
        if ($user->isProfileIncomplete() && !$user->profile_completion_modal_shown) {
            // Check if we're already on the profile completion page
            $path = $request->path();
            $isProfileCompletionPage = str_contains($path, 'admin/profile-completion') || 
                                       str_contains($path, 'profile-completion');
            
            // Prevent redirect loops - if we're already on the profile completion page, allow access
            if ($isProfileCompletionPage) {
                return $next($request);
            }
            
            // Only redirect if not already on profile completion page and not an AJAX request
            if (!$request->ajax() && !$request->wantsJson()) {
                return redirect()
                    ->to('/admin/profile-completion')
                    ->with('info', 'Please complete your company profile to continue.');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the request should bypass profile completeness check
     */
    protected function shouldAllowAccess(Request $request): bool
    {
        // Allow AJAX and JSON requests to prevent redirect loops
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }

        $allowedRoutes = [
            'filament.admin.pages.profile-completion',
            'logout',
            'filament.admin.auth.logout',
        ];

        foreach ($allowedRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        // Allow access to Filament core resources and auth pages
        $path = $request->path();
        if (
            str_contains($path, 'admin/auth') ||
            str_contains($path, 'admin/api') ||
            str_contains($path, 'livewire/') ||
            str_contains($path, 'admin/profile-completion') ||
            str_contains($path, 'profile-completion') ||
            str_contains($path, 'email/verify') ||
            str_contains($path, 'email-verification')
        ) {
            return true;
        }

        return false;
    }
}
