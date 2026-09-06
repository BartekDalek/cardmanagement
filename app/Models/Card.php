<?php

namespace App\Models;

use Database\Factories\CardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $card_number
 * @property string $card_number_hash
 * @property string $pin
 * @property Carbon $activation_date
 * @property Carbon $expiration_date
 * @property string $balance
 * @property-read string $masked_card_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['card_number', 'pin', 'activation_date', 'expiration_date', 'balance'])]
#[Hidden(['card_number', 'card_number_hash', 'pin'])]
class Card extends Model
{
    /** @use HasFactory<CardFactory> */
    use HasFactory;

    /**
     * Keep the blind index in sync with the encrypted card number.
     */
    protected static function booted(): void
    {
        static::saving(function (Card $card): void {
            $card->card_number_hash = self::hashCardNumber($card->card_number);
        });
    }

    /**
     * Hash a card number for lookups, since encrypted values cannot be queried.
     */
    public static function hashCardNumber(string $cardNumber): string
    {
        return hash_hmac('sha256', $cardNumber, (string) config('app.key'));
    }

    /**
     * The card number with everything except the last four digits masked.
     *
     * @return Attribute<string, never>
     */
    protected function maskedCardNumber(): Attribute
    {
        return Attribute::get(fn (): string => Str::mask($this->card_number, '*', 0, -4));
    }

    /**
     * Get the attributes that should be cast.
     *
     * The activation date is serialized in the format expected by datetime-local inputs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'card_number' => 'encrypted',
            'pin' => 'encrypted',
            'activation_date' => 'datetime:Y-m-d\TH:i',
            'expiration_date' => 'date:Y-m-d',
            'balance' => 'decimal:2',
        ];
    }
}
