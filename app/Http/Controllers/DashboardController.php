<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Models\Card;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'cards' => Gate::allows('viewAny', Card::class)
                ? CardResource::collection(Card::query()->orderByDesc('id')->paginate(10))
                : null,
        ]);
    }
}
