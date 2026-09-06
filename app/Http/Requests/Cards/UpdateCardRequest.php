<?php

namespace App\Http\Requests\Cards;

use App\Concerns\CardValidationRules;
use App\Models\Card;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
{
    use CardValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->card()) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->cardRules($this->card());
    }

    /**
     * Get the card being updated.
     */
    protected function card(): Card
    {
        /** @var Card $card */
        $card = $this->route('card');

        return $card;
    }
}
