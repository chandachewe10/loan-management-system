<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // NativeServiceProvider (vendor) hardcodes queue.default => 'database',
        // overriding our .env QUEUE_CONNECTION=sync. We restore sync here so that
        // exports and all queued jobs run immediately (no background worker needed).
        config(['queue.default' => 'sync']);

        // Run migrations to ensure the SQLite database is up to date
        Artisan::call('migrate', ['--force' => true]);

        // Seed Shield permissions if not already done
        // We use a simple flag in the DB to avoid re-running every boot
        $this->bootShieldIfNeeded();

        Window::open();
    }

    /**
     * Run the NativeShieldSeeder only on first boot
     * (or if the permissions table is empty).
     */
    protected function bootShieldIfNeeded(): void
    {
        try {
            // Re-seed if the permissions table doesn't exist yet, or if the count
            // is less than the full set defined in NativeShieldSeeder (401 permissions).
            // This ensures new permissions added in updates are always picked up.
            $expectedCount = 401;
            $currentCount  = Schema::hasTable('permissions')
                ? DB::table('permissions')->count()
                : 0;

            if ($currentCount < $expectedCount) {
                app(\Database\Seeders\NativeShieldSeeder::class)->run();
            }

            // Always ensure every existing user has the super_admin role
            // (handles the case where user registered before the seeder ran)
            $this->assignSuperAdminToAllUsers();

        } catch (\Throwable $e) {
            logger()->error('Shield boot failed: ' . $e->getMessage());
        }
    }

    /**
     * Assign super_admin ONLY to users who have NO roles at all.
     *
     * This handles the edge case where a user registered on a fresh NativePHP
     * install before the Shield seeder had a chance to create the roles table.
     * Users who were deliberately given a specific role (e.g. Loan Officer)
     * are never touched.
     */
    protected function assignSuperAdminToAllUsers(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('roles')) {
            return;
        }

        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->first();

        if (!$superAdminRole) {
            return;
        }

        // Only fix users who have NO roles assigned at all
        \App\Models\User::all()->each(function ($user) use ($superAdminRole) {
            if ($user->roles->isEmpty()) {
                $user->assignRole($superAdminRole);
                logger()->info('Auto-assigned super_admin to roleless user: ' . $user->email);
            }
        });
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
