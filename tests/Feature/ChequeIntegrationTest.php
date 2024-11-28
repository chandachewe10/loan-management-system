<?php

namespace Tests\Feature;

use App\Models\Cheque;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChequeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a cheque.
     */
    public function test_user_can_create_a_cheque()
    {
        // Simulate authenticated user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Data for new cheque
        $data = [
            'check_number' => 'CHK123',
            'amount' => 500.00,
            'beneficiary' => 'John Doe'
        ];

        // Send POST request
        $response = $this->post(route('cheque.store'), $data);

        // Assert database has the new cheque
        $this->assertDatabaseHas('cheques', $data);

        // Assert redirect after creation
        $response->assertRedirect(route('cheque.index'));
    }

    /**
     * Test reading (showing) a cheque.
     */
    public function test_user_can_view_a_cheque()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a cheque
        $cheque = Cheque::factory()->create();

        // Send GET request
        $response = $this->get(route('cheque.show', $cheque->id));

        // Assert response is successful
        $response->assertStatus(200);
        $response->assertSeeText($cheque->check_number); // Check if check_number is displayed
        $response->assertSeeText(number_format($cheque->amount));   // Check if amount is displayed
        $response->assertSeeText($cheque->beneficiary);  // Check if beneficiary is displayed
    }

    /**
     * Test updating a cheque.
     */
    public function test_user_can_update_a_cheque()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a cheque
        $cheque = Cheque::factory()->create();

        // Data for update
        $updatedData = [
            'check_number' => 'CHK456',
            'amount' => 1000.00,
            'beneficiary' => 'Jane Doe'
        ];

        // Send PUT request
        $response = $this->put(route('cheque.update', $cheque->id), $updatedData);

        // Assert database has updated cheque
        $this->assertDatabaseHas('cheques', $updatedData);

        // Assert database does not have old data
        $this->assertDatabaseMissing('cheques', ['check_number' => $cheque->check_number]);

        // Assert redirect after update
        $response->assertRedirect(route('cheque.index'));
    }

    /**
     * Test deleting a cheque.
     */
    public function test_user_can_delete_cheque_with_password()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a cheque record
        $cheque = Cheque::factory()->create();

        // Simulate a DELETE request to delete the cheque
        $response = $this->delete(route('cheque.destroy', $cheque->id), [
            'password' => '12345678',
            '_method' => 'DELETE',
        ]);

        // Assert that the response redirects (status 302)
        $response->assertStatus(302);

        // Assert that the cheque no longer exists in the database
        $this->assertSoftDeleted('cheques', [
            'id' => $cheque->id,
        ]);
    }

    /**
     * Test deleting a cheque without a password fails.
     */
    public function test_user_cannot_delete_cheque_without_password()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a cheque record
        $cheque = Cheque::factory()->create();

        // Simulate a DELETE request without a password
        $response = $this->delete(route('cheque.destroy', $cheque->id), []);

        // Assert that the response fails
        $response->assertStatus(302);

        // Assert that the cheque still exists in the database
        $this->assertDatabaseHas('cheques', [
            'id' => $cheque->id,
        ]);
    }

    /**
     * Test validation fails when creating a cheque with missing data.
     */
    public function test_validation_fails_for_missing_fields_on_create()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Send POST request with empty data
        $response = $this->post(route('cheque.store'), []);

        // Assert validation errors for each field
        $response->assertSessionHasErrors(['check_number', 'amount', 'beneficiary']);
    }

    /**
     * Test validation fails when updating a cheque with a duplicate check_number.
     */
    public function test_validation_fails_for_duplicate_check_number_on_update()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create two cheques
        $cheque1 = Cheque::factory()->create(['check_number' => 'CHK123']);
        $cheque2 = Cheque::factory()->create(['check_number' => 'CHK456']);

        // Attempt to update cheque2 with cheque1's check_number
        $response = $this->put(route('cheque.update', $cheque2->id), [
            'check_number' => 'CHK123',
            'amount' => $cheque2->amount,
            'beneficiary' => $cheque2->beneficiary
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['check_number']);
    }
}
