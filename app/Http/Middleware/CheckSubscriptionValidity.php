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

        // Allow access to subscription pages
        if ($request->routeIs('filament.admin.resources.subscriptions.index') ||
            $request->routeIs('filament.admin.resources.subscriptions.create')) {
            return $next($request);
        }

        // Check for expired subscription only for authenticated users
        $latestSubscription = Payments::where('payment_expires_at', '<', Carbon::now())
            ->where('organization_id', auth()->user()->organization_id)
            ->latest()
            ->first();

        if ($latestSubscription) {
            return redirect()
                ->route('filament.admin.resources.subscriptions.index')
                ->withErrors(['Your subscription has expired.']);
        }

        return $next($request);
    }
}
