<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CardController from '@/actions/App/Http/Controllers/CardController';
import CardNumber from '@/components/CardNumber.vue';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import type { Card, Paginated } from '@/types';

defineProps<{
    cards: Paginated<Card> | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

function confirmDelete(): boolean {
    return window.confirm('Delete this card?');
}
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <Head title="Dashboard" />

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                title="Dashboard"
                :description="
                    cards
                        ? 'You have access to gift cards. Manage your cards below.'
                        : 'You do not have access to gift cards. Card management is available only to administrators.'
                "
            />

            <Button v-if="cards" as-child>
                <Link :href="CardController.create()">New card</Link>
            </Button>
        </div>

        <template v-if="cards">
            <div class="overflow-x-auto rounded-xl border">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="p-3 font-medium">Card number</th>
                            <th class="p-3 font-medium">PIN</th>
                            <th class="p-3 font-medium">Activation date</th>
                            <th class="p-3 font-medium">Expiration date</th>
                            <th class="p-3 text-right font-medium">Balance</th>
                            <th class="p-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="cards.data.length === 0">
                            <td
                                colspan="6"
                                class="text-muted-foreground p-6 text-center"
                            >
                                No cards yet.
                            </td>
                        </tr>

                        <tr
                            v-for="card in cards.data"
                            :key="card.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 font-mono">
                                <CardNumber :card="card" />
                            </td>
                            <td class="p-3 font-mono">{{ card.pin }}</td>
                            <td class="p-3">{{ card.activation_date }}</td>
                            <td class="p-3">{{ card.expiration_date }}</td>
                            <td class="p-3 text-right">{{ card.balance }}</td>
                            <td class="p-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                    >
                                        <Link
                                            :href="CardController.edit(card.id)"
                                        >
                                            Edit
                                        </Link>
                                    </Button>

                                    <Form
                                        v-bind="
                                            CardController.destroy.form(card.id)
                                        "
                                        :options="{ preserveScroll: true }"
                                        @before="confirmDelete"
                                        v-slot="{ processing }"
                                    >
                                        <Button
                                            type="submit"
                                            variant="destructive"
                                            size="sm"
                                            :disabled="processing"
                                        >
                                            Delete
                                        </Button>
                                    </Form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="cards.links" :meta="cards.meta" />
        </template>
    </div>
</template>
