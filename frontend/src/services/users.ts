import api from './api'
import type { PaginatedResponse, User, UserFilters } from '@/types/user'

export async function fetchUsers(filters: Partial<UserFilters> = {}): Promise<PaginatedResponse<User>> {
    const params: Record<string, string | number> = {}

    if (filters.name) params.name = filters.name
    if (filters.cpf) params.cpf = filters.cpf.replace(/\D/g, '')
    if (filters.per_page) params.per_page = filters.per_page
    if (filters.page) params.page = filters.page

    const { data } = await api.get<PaginatedResponse<User>>('/users', { params })
    return data
}
