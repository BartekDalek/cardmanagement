<script setup lang="ts">
import { useHttp } from '@inertiajs/vue3';
import { Eye, EyeOff } from '@lucide/vue';
import { toast } from 'vue-sonner';
import { show } from '@/actions/App/Http/Controllers/CardController';
import { Button } from '@/components/ui/button';
import type { Card } from '@/types';

const props = defineProps<{ card: Card }>();
const http = useHttp<Record<string, never>, { card_number: string }>();

async function toggleNumber(): Promise<void> {
    if (http.response) {
        http.response = null;
        return;
    }

    try {
        await http.submit(show(props.card.id));
    } catch {
        toast.error('Could not reveal the card number. Please try again.');
    }
}
</script>

<template>
    <div class="flex items-center gap-2 whitespace-nowrap">
        <span>{{ http.response?.card_number ?? card.masked_card_number }}</span>
        <Button
            type="button"
            variant="ghost"
            size="icon-sm"
            :disabled="http.processing"
            :aria-label="
                http.response ? 'Hide card number' : 'Show card number'
            "
            :aria-pressed="!!http.response"
            @click="toggleNumber"
        >
            <EyeOff v-if="http.response" class="size-4" />
            <Eye v-else class="size-4" />
        </Button>
    </div>
</template>
