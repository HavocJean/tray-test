import api from './api'
import type { AuthResponse, CompleteRegistrationPayload, GoogleUrlResponse, User } from '@/types/user'

export async function fetchGoogleAuthUrl(): Promise<string> {
    const { data } = await api.get<GoogleUrlResponse>('/oauth/google/url')
    return data.url
}

export async function exchangeGoogleCode(code: string): Promise<string> {
    const { data } = await api.get<AuthResponse>('/oauth/google/callback', {
        params: { code },
    })
    return data.token
}

export async function completeRegistration(payload: CompleteRegistrationPayload): Promise<User> {
    const { data } = await api.put<{ data: User }>('/users/complete', payload)
    return data.data
}
