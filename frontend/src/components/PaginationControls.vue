<template>
    <div v-if="meta.last_page > 1" class="pagination">
        <button
            class="page-btn page-arrow"
            :disabled="meta.current_page <= 1"
            @click="$emit('page', meta.current_page - 1)"
        >
            &lsaquo;
        </button>

        <template v-for="item in pages" :key="item">
            <span v-if="item === '...'" class="page-ellipsis">...</span>
            <button
                v-else
                class="page-btn"
                :class="{ active: item === meta.current_page }"
                @click="$emit('page', item as number)"
            >
                {{ item }}
            </button>
        </template>

        <button
            class="page-btn page-arrow"
            :disabled="meta.current_page >= meta.last_page"
            @click="$emit('page', meta.current_page + 1)"
        >
            &rsaquo;
        </button>

    </div>
    <div class="page-total">
        {{ meta.total }} resultado{{ meta.total !== 1 ? 's' : '' }}
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue'
    import type { PaginationMeta } from '@/types/user'

    const props = defineProps<{ meta: PaginationMeta }>()
    defineEmits<{ page: [number] }>()

    const pages = computed(() => {
        const current = props.meta.current_page
        const last = props.meta.last_page
        const items: (number | string)[] = []

        if (last <= 7) {
            for (let i = 1; i <= last; i++) items.push(i)
            return items
        }

        items.push(1)

        if (current > 3) items.push('...')

        const start = Math.max(2, current - 1)
        const end = Math.min(last - 1, current + 1)

        for (let i = start; i <= end; i++) items.push(i)

        if (current < last - 2) items.push('...')

        items.push(last)

        return items
    })
</script>

<style scoped lang="scss">
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        margin-top: 1.25rem;
        flex-wrap: wrap;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 0.5rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: $color-text-secondary;
        background: $color-white;
        border: 1px solid $color-border;
        border-radius: $border-radius;
        cursor: pointer;
        transition: all 0.15s;

        &:hover:not(:disabled):not(.active) {
            border-color: $color-accent;
            color: $color-accent;
        }

        &.active {
            background: $color-accent;
            border-color: $color-accent;
            color: $color-white;
        }

        &:disabled {
            opacity: 0.3;
            cursor: default;
        }
    }

    .page-arrow {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .page-ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        font-size: 0.85rem;
        color: $color-placeholder;
        user-select: none;
    }

    .page-total {
        text-align: center;
        font-size: 0.8rem;
        color: $color-placeholder;
        margin-top: 0.8rem;
        width: 100%;
    }
</style>
