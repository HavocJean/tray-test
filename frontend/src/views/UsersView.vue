<template>
    <div class="users-page">
        <header class="users-header">
            <h1>Lista de Usuários</h1>
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
import { formatCpfInput } from '@/utils/formatters'
import UserTable from '@/components/UserTable.vue'
import PaginationControls from '@/components/PaginationControls.vue'

const store = useUsersStore()
const auth = useAuthStore()
const router = useRouter()

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
    max-width: 960px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
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
    padding: 0.5rem 1.25rem;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid #dadce0;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    color: #7c3aed;
    transition: all 0.15s;

    &:hover {
        border-color: #7c3aed;
        background: #f5f3ff;
    }
}

.filters {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.filter-input {
    flex: 1;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    border: 1px solid #dadce0;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.2s;
    color: #202124;

    &::placeholder {
        color: #9aa0a6;
    }

    &:focus {
        border-color: #7c3aed;
    }
}

.filter-cpf {
    max-width: 220px;
}

.table-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 1.25rem;
    min-height: 200px;
}

.loading {
    text-align: center;
    color: #9aa0a6;
    padding: 3rem;
    font-size: 0.9rem;
}
</style>
