<template>
    <div v-if="meta.last_page > 1" class="pagination">
        <button :disabled="meta.current_page <= 1" @click="$emit('page', meta.current_page - 1)">
            &laquo; Anterior
        </button>

        <span class="pagination-info">
            Página {{ meta.current_page }} de {{ meta.last_page }}
            <span class="pagination-total">({{ meta.total }} registros)</span>
        </span>

        <button :disabled="meta.current_page >= meta.last_page" @click="$emit('page', meta.current_page + 1)">
            Próxima &raquo;
        </button>
    </div>
    <div v-else class="pagination-info-single">
        {{ meta.total }} registro{{ meta.total !== 1 ? 's' : '' }}
    </div>
</template>

<script setup lang="ts">
    import type { PaginationMeta } from '@/types/user'

    defineProps<{ meta: PaginationMeta }>()
    defineEmits<{ page: [number] }>()
</script>

<style scoped lang="scss">
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;

        button {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            border: 1px solid #dadce0;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
            transition: background-color 0.15s;

            &:hover:not(:disabled) {
                background: #f5f5f5;
            }

            &:disabled {
                opacity: 0.4;
                cursor: default;
            }
        }
    }

    .pagination-info {
        font-size: 0.8rem;
        color: #5f6368;
    }

    .pagination-total {
        color: #9aa0a6;
    }

    .pagination-info-single {
        text-align: center;
        font-size: 0.8rem;
        color: #9aa0a6;
        margin-top: 0.75rem;
    }
</style>
