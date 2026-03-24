import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { fetchGoogleAuthUrl, exchangeGoogleCode, completeRegistration } from '@/services/auth'
import type { CompleteRegistrationPayload, User } from '@/types/user'

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('token'))
    const user = ref<User | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    const isAuthenticated = computed(() => token.value !== null)
    const needsRegistration = computed(() => isAuthenticated.value && user.value?.cpf === null)

    function setToken(newToken: string) {
        token.value = newToken
        localStorage.setItem('token', newToken)
    }

    function clearAuth() {
        token.value = null
        user.value = null
        localStorage.removeItem('token')
    }

    async function redirectToGoogle() {
        loading.value = true
        error.value = null
        try {
            const url = await fetchGoogleAuthUrl()
            window.location.href = url
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Erro ao conectar com Google.'
            loading.value = false
        }
    }

    async function handleGoogleCallback(code: string) {
        loading.value = true
        error.value = null
        try {
            const newToken = await exchangeGoogleCode(code)
            setToken(newToken)
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Erro ao autenticar com Google.'
        } finally {
            loading.value = false
        }
    }

    async function submitRegistration(payload: Omit<CompleteRegistrationPayload, 'token'>) {
        if (!token.value) return
        loading.value = true
        error.value = null
        try {
            user.value = await completeRegistration({ ...payload, token: token.value })
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Erro ao completar cadastro.'
            throw e
        } finally {
            loading.value = false
        }
    }

    return {
        token, user, loading, error,
        isAuthenticated, needsRegistration,
        setToken, clearAuth,
        redirectToGoogle, handleGoogleCallback, submitRegistration,
    }
})
