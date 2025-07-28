<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Customer\Create;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_is_created()
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount('customers', 0);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'Test')
            ->set('billing_email', $user->email)
            ->call('create');

        $this->assertEquals(1, Customer::count());
    }
}
