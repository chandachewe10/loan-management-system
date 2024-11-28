<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Cheque;

class ChequeCrudTest extends DuskTestCase
{
    /**
     * Test CRUD operations for cheques.
     */
    public function test_user_can_perform_crud_operations_on_cheques()
    {
        $this->browse(function (Browser $browser) {
            // Login as a user
            $user = User::factory()->create(['password' => bcrypt('password123')]);
            $browser->loginAs($user);

            // 1. CREATE: Create a new cheque
            $browser->visit('/cheque/create')
                ->type('input[name=check_number]', 'CHK123456')
                ->type('input[name=amount]', '1500')
                ->type('input[name=beneficiary]', 'John Doe')
                ->press('Save Changes')
                ->assertPathIs('/cheque')
                ->assertSee('CHK123456')
                ->assertSee('1,500')
                ->assertSee('John Doe');

            // 2. READ: Verify the cheque is listed on the index page
            $browser->visit('/cheque')
                ->assertSee('CHK123456')
                ->assertSee('1,500')
                ->assertSee('John Doe');

            // 3. UPDATE: Update the cheque
            $cheque = Cheque::where('check_number', 'CHK123456')->first();
            $browser->visit("/cheque/{$cheque->id}/edit")
                ->type('check_number', 'CHK654321')
                ->type('amount', '2000')
                ->type('beneficiary', 'Jane Doe')
                ->press('Save Changes')
                ->assertPathIs('/cheque')
                ->assertSee('CHK654321')
                ->assertSee('2,000')
                ->assertSee('Jane Doe');

            // 4. DELETE: Delete the cheque
            $cheque = Cheque::where('check_number', 'CHK654321')->first();
            $browser->visit('/cheque')
                ->assertSee($cheque->check_number)
                ->click('.btn-danger[data-target="#passwordConfirmationModal' . $cheque->id . '"]')
                ->waitFor('#passwordConfirmationModal' . $cheque->id) // Wait for the modal to appear
                ->within('#passwordConfirmationModal' . $cheque->id, function (Browser $modal) use ($cheque) {
                    $modal->type('#password' . $cheque->id, 'password123') // Type the password
                        ->press('Confirm Delete'); // Press the Confirm Delete button
                })
                ->waitUntilMissing('#passwordConfirmationModal' . $cheque->id) // Wait for the modal to disappear
                ->pause(2000) // Optional: small delay to allow any post-deletion UI updates
                ->assertDontSee($cheque->check_number); // Assert that cheque is no longer visible
        });
    }
}
