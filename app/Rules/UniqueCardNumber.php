<?php

namespace App\Rules;

use App\Models\Card;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class UniqueCardNumber implements ValidationRule
{
    public function __construct(private ?Card $ignoredCard = null) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $query = Card::query()->where('card_number_hash', Card::hashCardNumber($value));

        if ($this->ignoredCard !== null) {
            $query->whereKeyNot($this->ignoredCard);
        }

        if ($query->exists()) {
            $fail('validation.unique')->translate();
        }
    }
}
