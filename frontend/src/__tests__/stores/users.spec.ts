import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useUsersStore } from '@/stores/users'
import * as usersService from '@/services/users'
import type { PaginatedResponse, User } from '@/types/user'

vi.mock('@/services/users')

const mockUser: User = {
    uuid: 'abc-123',
    name: 'Maria Silva',
    email: 'maria@example.com',
    cpf: '12345678901',
    birth_date: '1990-05-15',
    created_at: '2026-03-20T10:00:00Z',
}

const mockResponse: PaginatedResponse<User> = {
    data: [mockUser],
    meta: {
        current_page: 1,
        last_page: 3,
        per_page: 20,
        from: 1,
        to: 1,
        total: 50,
    },
}

describe('useUsersStore', () => {
    beforeEach(() => {
        setActivePinia(createPinia())
        vi.restoreAllMocks()
    })

    it('carrega usuários e atualiza estado', async () => {
        vi.mocked(usersService.fetchUsers).mockResolvedValue(mockResponse)

        const store = useUsersStore()
        await store.loadUsers()

        expect(store.users).toEqual([mockUser])
        expect(store.meta.total).toBe(50)
        expect(store.meta.last_page).toBe(3)
        expect(store.loading).toBe(false)
        expect(store.error).toBeNull()
    })

    it('define error ao falhar', async () => {
        vi.mocked(usersService.fetchUsers).mockRejectedValue({
            response: { data: { message: 'Erro no servidor.' } },
        })

        const store = useUsersStore()
        await store.loadUsers()

        expect(store.users).toEqual([])
        expect(store.error).toBe('Erro no servidor.')
    })

    it('goToPage chama loadUsers com a página correta', async () => {
        vi.mocked(usersService.fetchUsers).mockResolvedValue(mockResponse)

        const store = useUsersStore()
        await store.loadUsers()

        store.meta.last_page = 5
        await store.goToPage(3)

        expect(usersService.fetchUsers).toHaveBeenLastCalledWith(
            expect.objectContaining({ page: 3 })
        )
    })

    it('goToPage ignora página inválida', async () => {
        vi.mocked(usersService.fetchUsers).mockResolvedValue(mockResponse)

        const store = useUsersStore()
        await store.loadUsers()
        const callCount = vi.mocked(usersService.fetchUsers).mock.calls.length

        store.goToPage(0)
        store.goToPage(store.meta.last_page + 1)

        expect(usersService.fetchUsers).toHaveBeenCalledTimes(callCount)
    })

    it('$reset limpa todo o estado', async () => {
        vi.mocked(usersService.fetchUsers).mockResolvedValue(mockResponse)

        const store = useUsersStore()
        await store.loadUsers()
        store.filters.name = 'test'

        store.$reset()

        expect(store.users).toEqual([])
        expect(store.filters.name).toBe('')
        expect(store.meta.total).toBe(0)
    })
})
