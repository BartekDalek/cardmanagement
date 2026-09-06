<?php

namespace App\Concerns;

use App\Models\Card;
use App\Rules\UniqueCardNumber;
use Illuminate\Contracts\Validation\ValidationRule;

trait CardValidationRules
{
    /**
     * Get the validation rules used to validate cards.
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    protected function cardRules(?Card $ignoredCard = null): array
    {
        return [
            'card_number' => ['required', 'digits:20', new UniqueCardNumber($ignoredCard)],
            'pin' => ['required', 'digits:4'],
            'activation_date' => ['required', 'date_format:Y-m-d\TH:i'],
            'expiration_date' => ['required', 'date_format:Y-m-d', 'after:activation_date'],
            'balance' => ['required', 'decimal:0,2', 'min:0', 'max:99999999.99'],
        ];
    }
}
