<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { CardFormValues } from '@/types';

defineProps<{
    card?: CardFormValues;
    errors: Record<string, string>;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-2">
            <Label for="card_number">Card number</Label>
            <Input
                id="card_number"
                name="card_number"
                inputmode="numeric"
                maxlength="20"
                :default-value="card?.card_number"
                required
                autocomplete="off"
                placeholder="20 digits"
            />
            <InputError :message="errors.card_number" />
        </div>

        <div class="grid gap-2">
            <Label for="pin">PIN</Label>
            <Input
                id="pin"
                name="pin"
                inputmode="numeric"
                maxlength="4"
                :default-value="card?.pin"
                required
                autocomplete="off"
                placeholder="4 digits"
            />
            <InputError :message="errors.pin" />
        </div>

        <div class="grid gap-2">
            <Label for="activation_date">Activation date</Label>
            <Input
                id="activation_date"
                name="activation_date"
                type="datetime-local"
                :default-value="card?.activation_date"
                required
            />
            <InputError :message="errors.activation_date" />
        </div>

        <div class="grid gap-2">
            <Label for="expiration_date">Expiration date</Label>
            <Input
                id="expiration_date"
                name="expiration_date"
                type="date"
                :default-value="card?.expiration_date"
                required
            />
            <InputError :message="errors.expiration_date" />
        </div>

        <div class="grid gap-2">
            <Label for="balance">Balance</Label>
            <Input
                id="balance"
                name="balance"
                type="number"
                step="0.01"
                min="0"
                :default-value="card?.balance"
                required
                placeholder="0.00"
            />
            <InputError :message="errors.balance" />
        </div>
    </div>
</template>
