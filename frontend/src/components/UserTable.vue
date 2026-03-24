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

    defineProps<{ users: User[] }>()

    function formatCpf(cpf: string | null): string {
        if (!cpf) return '—'
        const d = cpf.replace(/\D/g, '')
        if (d.length !== 11) return cpf
        return `${d.slice(0, 3)}.${d.slice(3, 6)}.${d.slice(6, 9)}-${d.slice(9)}`
    }

    function formatDate(date: string | null): string {
        if (!date) return '—'
        const [y, m, d] = date.split('-')
        return `${d}/${m}/${y}`
    }
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
            border-bottom: 1px solid #f0f0f0;
            white-space: nowrap;
        }

        th {
            font-weight: 600;
            font-size: 0.8rem;
            color: #7c7c8a;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background: transparent;
        }

        td {
            color: #202124;
        }

        tbody tr {
            transition: background-color 0.1s;

            &:hover td {
                background: #f5f3ff;
            }
        }

        .empty {
            text-align: center;
            color: #9aa0a6;
            padding: 2.5rem;
            font-style: italic;
        }
    }
</style>
