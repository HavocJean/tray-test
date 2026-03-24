<template>
    <div class="table-wrap">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Data de Nascimento</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="users.length === 0">
                    <td colspan="4" class="empty">Nenhum usuário encontrado.</td>
                </tr>
                <tr v-for="user in users" :key="user.uuid">
                    <td>{{ user.name ?? '—' }}</td>
                    <td>{{ user.email ?? '—' }}</td>
                    <td>{{ formatCpf(user.cpf) }}</td>
                    <td>{{ formatDate(user.birth_date) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
    import type { User } from '@/types/user'
    import { formatCpf, formatDate } from '@/utils/formatters'

    defineProps<{ users: User[] }>()
</script>

<style scoped lang="scss">
    .table-wrap {
        overflow-x: auto;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;

        th, td {
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid $color-border-lighter;
            white-space: nowrap;
        }

        th {
            font-weight: 600;
            font-size: 0.8rem;
            color: $color-muted;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background: transparent;
        }

        td {
            color: $color-text;
        }

        tbody tr {
            transition: background-color 0.1s;

            &:hover td {
                background: $color-accent-bg;
            }
        }

        .empty {
            text-align: center;
            color: $color-placeholder;
            padding: 2.5rem;
            font-style: italic;
        }
    }
</style>
