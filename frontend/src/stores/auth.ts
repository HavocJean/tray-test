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

    function openGooglePopup(): Promise<string> {
        return new Promise((resolve, reject) => {
            fetchGoogleAuthUrl().then((url) => {
                const w = 500
                const h = 600
                const left = window.screenX + (window.outerWidth - w) / 2
                const top = window.screenY + (window.outerHeight - h) / 2
                const popup = window.open(
                    url,
                    'google-login',
                    `width=${w},height=${h},left=${left},top=${top},popup=yes`
                )

                if (!popup) {
                    reject(new Error('Popup bloqueado pelo navegador.'))
                    return
                }

                const onMessage = (event: MessageEvent) => {
                    if (event.origin !== window.location.origin) return
                    if (event.data?.type === 'oauth-success' && event.data.token) {
                        window.removeEventListener('message', onMessage)
                        clearInterval(pollTimer)
                        resolve(event.data.token)
                    }
                    if (event.data?.type === 'oauth-error') {
                        window.removeEventListener('message', onMessage)
                        clearInterval(pollTimer)
                        reject(new Error(event.data.message ?? 'Erro na autenticação.'))
                    }
                }

                const pollTimer = setInterval(() => {
                    if (popup.closed) {
                        clearInterval(pollTimer)
                        window.removeEventListener('message', onMessage)
                        reject(new Error('Popup fechado antes de concluir.'))
                    }
                }, 500)

                window.addEventListener('message', onMessage)
            }).catch(reject)
        })
    }

    async function loginWithGoogle() {
        loading.value = true
        error.value = null
        try {
            const newToken = await openGooglePopup()
            setToken(newToken)
        } catch (e: any) {
            error.value = e.message ?? 'Erro ao autenticar com Google.'
        } finally {
            loading.value = false
        }
    }

    async function handleGoogleCallback(code: string) {
        loading.value = true
        error.value = null
        try {
            const newToken = await exchangeGoogleCode(code)
            setToken(newToken)
            return newToken
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Erro ao autenticar com Google.'
            throw e
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
        loginWithGoogle, handleGoogleCallback, submitRegistration,
    }
})
