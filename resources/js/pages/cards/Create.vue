<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CardController from '@/actions/App/Http/Controllers/CardController';
import CardFormFields from '@/components/CardFormFields.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'New card',
                href: CardController.create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="New card" />

    <div class="flex flex-col gap-6 p-4">
        <Heading title="New card" description="Add a gift card" />

        <Form
            v-bind="CardController.store.form()"
            class="max-w-lg space-y-6"
            v-slot="{ errors, processing }"
        >
            <CardFormFields :errors="errors" />

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create card</Button>
                <Button variant="ghost" as-child>
                    <Link :href="dashboard()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>
