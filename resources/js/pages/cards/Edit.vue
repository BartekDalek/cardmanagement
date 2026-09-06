<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CardController from '@/actions/App/Http/Controllers/CardController';
import CardFormFields from '@/components/CardFormFields.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import type { CardFormValues } from '@/types';

const props = defineProps<{
    card: CardFormValues & { id: number };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Edit card',
                href: '#',
            },
        ],
    },
});
</script>

<template>
    <Head title="Edit card" />

    <div class="flex flex-col gap-6 p-4">
        <Heading title="Edit card" description="Update the gift card details" />

        <Form
            v-bind="CardController.update.form(props.card.id)"
            class="max-w-lg space-y-6"
            v-slot="{ errors, processing }"
        >
            <CardFormFields :card="props.card" :errors="errors" />

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save changes</Button>
                <Button variant="ghost" as-child>
                    <Link :href="dashboard()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
