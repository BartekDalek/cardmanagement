<?php

namespace App\Http\Resources;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Card
 */
class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'masked_card_number' => $this->masked_card_number,
            'pin' => $this->pin,
            'activation_date' => $this->activation_date->format('Y-m-d H:i'),
            'expiration_date' => $this->expiration_date->toDateString(),
            'balance' => $this->balance,
        ];
    }
}
