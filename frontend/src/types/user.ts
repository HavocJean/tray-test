export interface User {
    uuid: string
    name: string | null
    email: string | null
    cpf: string | null
    birth_date: string | null
    created_at: string
}

export interface UserFilters {
    name: string
    cpf: string
    per_page: number
    page: number
}

export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    from: number | null
    to: number | null
    total: number
}

export interface PaginatedResponse<T> {
    data: T[]
    meta: PaginationMeta
}

export interface CompleteRegistrationPayload {
    token: string
    name: string
    cpf: string
    birth_date: string
}

export interface AuthResponse {
    token: string
}

export interface GoogleUrlResponse {
    url: string
}

export interface ApiError {
    message: string
    errors?: Record<string, string[]>
}
