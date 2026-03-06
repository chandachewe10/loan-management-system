<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PagePermissionsSeeder extends Seeder
{
    /**
     * Seed permissions for resource sub-pages that FilamentShield
     * cannot auto-detect (they extend Filament\Resources\Pages\Page,
     * not the standalone Filament\Pages\Page).
     */
    public function run(): void
    {
        $guard = 'web';

        $permissions = [
            'page_StatementOfFinancialPosition',
            'page_StatementOfComprehensiveIncome',
            'page_CashFlowStatement',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => $guard,
            ]);
        }

        // Ensure super_admin always has these
        $superAdmin = Role::where('name', 'super_admin')
            ->where('guard_name', $guard)
            ->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $this->command->info('✅ Page permissions seeded successfully.');
    }
}
