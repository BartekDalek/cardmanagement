<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cards\StoreCardRequest;
use App\Http\Requests\Cards\UpdateCardRequest;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CardController extends Controller
{
    public function create(): Response
    {
        Gate::authorize('create', Card::class);

        return Inertia::render('cards/Create');
    }

    public function store(StoreCardRequest $request): RedirectResponse
    {
        Card::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Card created.')]);

        return to_route('dashboard');
    }

    public function show(Card $card): JsonResponse
    {
        Gate::authorize('view', $card);

        return response()->json(['card_number' => $card->card_number])
            ->header('Cache-Control', 'private, no-store');
    }

    public function edit(Card $card): Response
    {
        Gate::authorize('update', $card);

        return Inertia::render('cards/Edit', [
            'card' => [
                'id' => $card->id,
                'card_number' => $card->card_number,
                'pin' => $card->pin,
                'activation_date' => $card->activation_date->format('Y-m-d\TH:i'),
                'expiration_date' => $card->expiration_date->toDateString(),
                'balance' => $card->balance,
            ],
        ]);
    }

    public function update(UpdateCardRequest $request, Card $card): RedirectResponse
    {
        $card->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Card updated.')]);

        return to_route('dashboard');
    }

    public function destroy(Card $card): RedirectResponse
    {
        Gate::authorize('delete', $card);

        $card->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Card deleted.')]);

        return to_route('dashboard');
    }
}
