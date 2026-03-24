<template>
    <div class="users-page">
        <header class="users-header">
            <h1>Usuários</h1>
            <button class="btn-logout" @click="logout">Sair</button>
        </header>

        <div class="filters">
            <input
                v-model="store.filters.name"
                type="text"
                placeholder="Filtrar por nome..."
                class="filter-input"
            />
            <input
                v-model="store.filters.cpf"
                type="text"
                placeholder="Filtrar por CPF..."
                class="filter-input filter-cpf"
                maxlength="14"
                @input="onCpfInput"
            />
        </div>

        <div v-if="store.error" class="alert-error">{{ store.error }}</div>

        <div class="table-card">
            <div v-if="store.loading" class="loading">Carregando...</div>
            <template v-else>
                <UserTable :users="store.users" />
                <PaginationControls :meta="store.meta" @page="store.goToPage" />
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'
import { useAuthStore } from '@/stores/auth'
import UserTable from '@/components/UserTable.vue'
import PaginationControls from '@/components/PaginationControls.vue'

const store = useUsersStore()
const auth = useAuthStore()
const router = useRouter()

function formatCpfInput(value: string): string {
    const digits = value.replace(/\D/g, '').slice(0, 11)
    if (digits.length <= 3) return digits
    if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`
    if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`
    return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`
}

function onCpfInput(e: Event) {
    const input = e.target as HTMLInputElement
    store.filters.cpf = formatCpfInput(input.value)
}

function logout() {
    auth.clearAuth()
    store.$reset()
    router.replace({ name: 'login' })
}

onMounted(() => {
    store.loadUsers()
})
</script>

<style scoped lang="scss">
.users-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.users-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;

    h1 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1a2e;
    }
}

.btn-logout {
    padding: 0.4rem 1rem;
    font-size: 0.8rem;
    border: 1px solid #dadce0;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
    color: #5f6368;
    transition: background-color 0.15s;

    &:hover {
        background: #f5f5f5;
    }
}

.filters {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.filter-input {
    flex: 1;
    padding: 0.625rem 0.75rem;
    font-size: 0.875rem;
    border: 1px solid #dadce0;
    border-radius: 6px;
    outline: none;
    transition: border-color 0.2s;

    &:focus {
        border-color: #7c3aed;
    }
}

.filter-cpf {
    max-width: 200px;
}

.table-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    min-height: 200px;
}

.loading {
    text-align: center;
    color: #9aa0a6;
    padding: 3rem;
}
</style>
