<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { PaginationLinks, PaginationMeta } from '@/types';

defineProps<{
    links: PaginationLinks;
    meta: PaginationMeta;
}>();
</script>

<template>
    <nav
        v-if="meta.last_page > 1"
        class="flex items-center justify-between"
        aria-label="Pagination"
    >
        <p class="text-muted-foreground text-sm">
            Showing {{ meta.from ?? 0 }}–{{ meta.to ?? 0 }} of {{ meta.total }}
        </p>

        <div class="flex gap-2">
            <Button v-if="links.prev" variant="outline" size="sm" as-child>
                <Link :href="links.prev">Previous</Link>
            </Button>
            <Button v-else variant="outline" size="sm" disabled>
                Previous
            </Button>

            <Button v-if="links.next" variant="outline" size="sm" as-child>
                <Link :href="links.next">Next</Link>
            </Button>
            <Button v-else variant="outline" size="sm" disabled>Next</Button>
        </div>
    </nav>
</template>
