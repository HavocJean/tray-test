import { defineStore } from 'pinia'
import { ref, reactive, watch } from 'vue'
import { fetchUsers } from '@/services/users'
import type { User, PaginationMeta } from '@/types/user'

export const useUsersStore = defineStore('users', () => {
    const users = ref<User[]>([])
    const meta = ref<PaginationMeta>({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        from: null,
        to: null,
        total: 0,
    })

    const filters = reactive({ name: '', cpf: '' })
    const loading = ref(false)
    const error = ref<string | null>(null)

    let debounceTimer: ReturnType<typeof setTimeout> | null = null

    async function loadUsers(page = 1) {
        loading.value = true
        error.value = null
        try {
            const res = await fetchUsers({
                name: filters.name,
                cpf: filters.cpf,
                per_page: meta.value.per_page,
                page,
            })
            users.value = res.data
            meta.value = res.meta
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Erro ao carregar usuários.'
        } finally {
            loading.value = false
        }
    }

    function goToPage(page: number) {
        if (page < 1 || page > meta.value.last_page) return
        loadUsers(page)
    }

    function applyFilters() {
        loadUsers(1)
    }

    watch(() => filters.name, () => {
        if (debounceTimer) clearTimeout(debounceTimer)
        debounceTimer = setTimeout(() => applyFilters(), 400)
    })

    watch(() => filters.cpf, () => {
        const digits = filters.cpf.replace(/\D/g, '')
        if (digits.length === 11 || digits.length === 0) {
            applyFilters()
        }
    })

    function $reset() {
        users.value = []
        filters.name = ''
        filters.cpf = ''
        meta.value = { current_page: 1, last_page: 1, per_page: 20, from: null, to: null, total: 0 }
        error.value = null
    }

    return {
        users, meta, filters, loading, error,
        loadUsers, goToPage, applyFilters, $reset,
    }
})
