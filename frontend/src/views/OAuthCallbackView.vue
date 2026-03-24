<template>
    <div class="page-center">
        <div class="card" style="text-align: center">
            <p v-if="auth.loading">Autenticando...</p>
            <div v-else-if="auth.error" class="alert-error">
                {{ auth.error }}
                <br />
                <router-link to="/" style="margin-top: 0.5rem; display: inline-block">Voltar ao login</router-link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { onMounted } from 'vue'
    import { useRoute, useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'

    const route = useRoute()
    const router = useRouter()
    const auth = useAuthStore()

    onMounted(async () => {
        const code = route.query.code as string | undefined

        if (!code) {
            auth.error = 'Código de autorização não encontrado.'
            return
        }

        await auth.handleGoogleCallback(code)

        if (!auth.error) {
            router.replace({ name: 'register' })
        }
    })
</script>
