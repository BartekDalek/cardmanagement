<?php

use App\Models\Card;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('regular users do not receive the card list', function () {
    Card::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (AssertableInertia $page) => $page->where('cards', null));
});

test('administrators receive cards paginated by ten', function () {
    Card::factory()->count(11)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('cards.data', 10)
            ->where('cards.meta.total', 11));
});

test('the card list exposes only the masked card number', function () {
    Card::factory()->create(['card_number' => '12345678901234567890']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('dashboard'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('cards.data.0.masked_card_number', '****************7890')
            ->missing('cards.data.0.card_number'));
});
