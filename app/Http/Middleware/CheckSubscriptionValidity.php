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
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }


     if ($request->routeIs('filament.admin.resources.subscriptions.index') || $request->routeIs('filament.admin.resources.subscriptions.create')
        ) {
        return $next($request);
    }

        $latestSubscription = Payments::where( 'payment_expires_at', '<',Carbon::now())
            ->where('organization_id',"=",auth()->user()->organization_id)
            ->latest()
            ->first();

        if ($latestSubscription) {

            return redirect()->route('filament.admin.resources.subscriptions.index')->withErrors(['Your subscription has expired.']);
        }

        return $next($request);
    }
}
