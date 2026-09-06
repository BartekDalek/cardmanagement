<?php

use App\Models\Card;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function validCardPayload(array $overrides = []): array
{
    return [
        'card_number' => '12345678901234567890',
        'pin' => '1234',
        'activation_date' => '2026-09-05T10:00',
        'expiration_date' => '2027-09-05',
        'balance' => '150.00',
        ...$overrides,
    ];
}

test('guests are redirected to the login page', function () {
    $this->get(route('cards.create'))->assertRedirect(route('login'));
});

test('regular users are forbidden from every card endpoint', function (string $method, string $routeName) {
    $card = Card::factory()->create();

    $this->actingAs(User::factory()->create())
        ->{$method}(route($routeName, ['card' => $card]))
        ->assertForbidden();
})->with([
    'create page' => ['get', 'cards.create'],
    'store' => ['post', 'cards.store'],
    'reveal number' => ['get', 'cards.show'],
    'edit page' => ['get', 'cards.edit'],
    'update' => ['put', 'cards.update'],
    'destroy' => ['delete', 'cards.destroy'],
]);

test('administrators can create a card', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->post(route('cards.store'), validCardPayload());

    $response->assertRedirect(route('dashboard'));
    expect(Card::query()->sole())
        ->card_number->toBe('12345678901234567890')
        ->pin->toBe('1234')
        ->activation_date->format('Y-m-d H:i')->toBe('2026-09-05 10:00')
        ->expiration_date->toDateString()->toBe('2027-09-05')
        ->balance->toBe('150.00');
});

test('card number and PIN are stored encrypted', function () {
    Card::factory()->create(['card_number' => '12345678901234567890', 'pin' => '1234']);

    $this->assertDatabaseMissing('cards', ['card_number' => '12345678901234567890']);
    $this->assertDatabaseMissing('cards', ['pin' => '1234']);
});

test('every card field is required', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('cards.store'), [])
        ->assertSessionHasErrors([
            'card_number' => 'The card number field is required.',
            'pin' => 'The pin field is required.',
            'activation_date' => 'The activation date field is required.',
            'expiration_date' => 'The expiration date field is required.',
            'balance' => 'The balance field is required.',
        ]);

    $this->assertDatabaseEmpty('cards');
});

test('invalid card values are rejected', function (array $input, string $field, string $message) {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('cards.store'), validCardPayload($input))
        ->assertSessionHasErrors([$field => $message]);

    $this->assertDatabaseEmpty('cards');
})->with([
    'card number shorter than 20 digits' => [['card_number' => '1234567890123456789'], 'card_number', 'The card number field must be 20 digits.'],
    'PIN longer than 4 digits' => [['pin' => '12345'], 'pin', 'The pin field must be 4 digits.'],
    'expiration date before activation date' => [['expiration_date' => '2026-09-04'], 'expiration_date', 'The expiration date field must be a date after activation date.'],
    'negative balance' => [['balance' => '-1'], 'balance', 'The balance field must be at least 0.'],
    'balance with more than two decimals' => [['balance' => '10.005'], 'balance', 'The balance field must have 0-2 decimal places.'],
]);

test('a card number that already exists is rejected', function () {
    Card::factory()->create(['card_number' => '12345678901234567890']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('cards.store'), validCardPayload())
        ->assertSessionHasErrors(['card_number' => 'The card number has already been taken.']);

    $this->assertDatabaseCount('cards', 1);
});

test('card secrets are not flashed to the session when card creation fails validation', function () {
    $response = $this->actingAs(User::factory()->admin()->create())
        ->from(route('cards.create'))
        ->post(route('cards.store'), validCardPayload(['balance' => '-1']));

    $response
        ->assertRedirect(route('cards.create'))
        ->assertSessionMissing('_old_input.card_number')
        ->assertSessionMissing('_old_input.pin')
        ->assertSessionHasInput('balance', '-1');
});

test('administrators can reveal the full card number', function () {
    $card = Card::factory()->create(['card_number' => '12345678901234567890']);

    $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('cards.show', $card))
        ->assertExactJson(['card_number' => '12345678901234567890'])
        ->assertHeader('Cache-Control', 'no-store, private');
});

test('the edit form is prefilled with the decrypted card data', function () {
    $card = Card::factory()->create(['card_number' => '12345678901234567890', 'pin' => '1234']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('cards.edit', $card))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('cards/Edit')
            ->where('card.card_number', '12345678901234567890')
            ->where('card.pin', '1234'));
});

test('administrators can update a card while keeping its number', function () {
    $card = Card::factory()->create(['card_number' => '12345678901234567890', 'pin' => '0000']);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->put(route('cards.update', $card), validCardPayload(['pin' => '9999', 'balance' => '20.50']));

    $response->assertRedirect(route('dashboard'));
    expect($card->refresh())
        ->pin->toBe('9999')
        ->balance->toBe('20.50');
});

test('updating a card to another card\'s number is rejected', function () {
    Card::factory()->create(['card_number' => '11111111111111111111']);
    $card = Card::factory()->create(['card_number' => '22222222222222222222']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('cards.update', $card), validCardPayload(['card_number' => '11111111111111111111']))
        ->assertSessionHasErrors(['card_number' => 'The card number has already been taken.']);

    expect($card->refresh()->card_number)->toBe('22222222222222222222');
});

test('administrators can delete a card', function () {
    $card = Card::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('cards.destroy', $card))
        ->assertRedirect(route('dashboard'));

    $this->assertModelMissing($card);
});

test('card requests are throttled after 30 per minute', function () {
    $card = Card::factory()->create();
    $this->actingAs(User::factory()->admin()->create());

    for ($request = 1; $request <= 30; $request++) {
        $this->getJson(route('cards.show', $card))->assertOk();
    }

    $this->getJson(route('cards.show', $card))->assertTooManyRequests();
});
