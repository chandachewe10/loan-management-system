<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Payments;

class CheckSubscriptionValidity
{
    public function handle(Request $request, Closure $next)
    {
        // First check if user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Allow access to subscription pages and other essential routes
        if ($this->shouldAllowAccess($request)) {
            return $next($request);
        }

        // Check for active subscription
        $todaysDate = Carbon::now();
        $activeSubscription = Payments::withoutGlobalScope('org')->where('payment_expires_at', '>', $todaysDate)
            ->where('organization_id', $user->organization_id)
            ->orderBy('payment_expires_at', 'desc') 
            ->first();

        // If no active subscription, redirect to subscriptions page
        // But allow access if user is on subscription/payment pages or profile completion
        if (!$activeSubscription) {
            $path = $request->path();
            $isSubscriptionPage = str_contains($path, 'admin/subscriptions') || 
                                  str_contains($path, 'admin/payments') ||
                                  str_contains($path, 'subscriptions') ||
                                  str_contains($path, 'payments');
            
            if (!$isSubscriptionPage) {
                return redirect()
                    ->route('filament.admin.resources.subscriptions.index')
                    ->with('error', 'Your subscription has expired. Please renew to continue accessing the system.');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the request should bypass subscription check
     */
    protected function shouldAllowAccess(Request $request): bool
    {
        // Allow AJAX and JSON requests to prevent redirect loops
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }

        $allowedRoutes = [
            'filament.admin.resources.subscriptions.*',
            'filament.admin.resources.payments.*',
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
