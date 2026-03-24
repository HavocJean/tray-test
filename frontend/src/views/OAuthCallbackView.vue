<template>
    <div class="page-center">
        <div class="card" style="text-align: center">
            <p v-if="processing">Autenticando...</p>
            <p v-else-if="errorMsg" class="alert-error">{{ errorMsg }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref, onMounted } from 'vue'
    import { useRoute } from 'vue-router'
    import { exchangeGoogleCode } from '@/services/auth'
    import { getApiErrorMessage } from '@/utils/error'

    const route = useRoute()
    const processing = ref(true)
    const errorMsg = ref<string | null>(null)

    onMounted(async () => {
        const code = route.query.code as string | undefined

        if (!code) {
            errorMsg.value = 'Código de autorização não encontrado.'
            processing.value = false
            notifyParent({ type: 'oauth-error', message: errorMsg.value })
            return
        }

        try {
            const token = await exchangeGoogleCode(code)
            notifyParent({ type: 'oauth-success', token })
        } catch (e: unknown) {
            errorMsg.value = getApiErrorMessage(e, 'Erro ao autenticar com Google.')
            notifyParent({ type: 'oauth-error', message: errorMsg.value })
        } finally {
            processing.value = false
        }
    })

    function notifyParent(data: Record<string, unknown>) {
        if (window.opener) {
            window.opener.postMessage(data, window.location.origin)
            setTimeout(() => window.close(), 300)
        }
    }
</script>
