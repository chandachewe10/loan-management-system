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

        // Check if profile is incomplete and modal hasn't been shown
        if ($user->isProfileIncomplete() && !$user->profile_completion_modal_shown) {
            // Only redirect if not already on the profile completion page
            if (!$request->routeIs('filament.admin.pages.profile-completion')) {
                return redirect()
                    ->route('filament.admin.pages.profile-completion')
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
            str_contains($path, 'livewire/')
        ) {
            return true;
        }

        return false;
    }
}
