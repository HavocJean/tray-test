<template>
    <div class="page-center">
        <div class="card">
            <h1 class="card-title">Completar cadastro</h1>

            <div v-if="auth.error" class="alert-error">{{ auth.error }}</div>

            <form @submit.prevent="onSubmit">
                <div class="form-group">
                    <label for="name">Nome completo</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        :class="{ 'is-invalid': errors.name }"
                        placeholder="Seu nome"
                    />
                    <span v-if="errors.name" class="field-error">{{ errors.name }}</span>
                </div>

                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input
                        id="cpf"
                        v-model="form.cpf"
                        type="text"
                        :class="{ 'is-invalid': errors.cpf }"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        @input="onCpfInput"
                    />
                    <span v-if="errors.cpf" class="field-error">{{ errors.cpf }}</span>
                </div>

                <div class="form-group">
                    <label for="birth_date">Data de nascimento</label>
                    <input
                        id="birth_date"
                        v-model="form.birth_date"
                        type="date"
                        :class="{ 'is-invalid': errors.birth_date }"
                    />
                    <span v-if="errors.birth_date" class="field-error">{{ errors.birth_date }}</span>
                </div>

                <button type="submit" class="btn-primary" :disabled="auth.loading">
                    {{ auth.loading ? 'Salvando...' : 'Salvar' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { reactive } from 'vue'
    import { useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'

    const auth = useAuthStore()
    const router = useRouter()

    const form = reactive({
        name: '',
        cpf: '',
        birth_date: '',
    })

    const errors = reactive<Record<string, string>>({
        name: '',
        cpf: '',
        birth_date: '',
    })

    function formatCpf(value: string): string {
        const digits = value.replace(/\D/g, '').slice(0, 11)
        if (digits.length <= 3) return digits
        if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`
        if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`
        return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`
    }

    function onCpfInput(e: Event) {
        const input = e.target as HTMLInputElement
        form.cpf = formatCpf(input.value)
    }

    function validate(): boolean {
        let valid = true
        errors.name = ''
        errors.cpf = ''
        errors.birth_date = ''

        if (!form.name.trim()) {
            errors.name = 'Nome é obrigatório.'
            valid = false
        }

        const cpfDigits = form.cpf.replace(/\D/g, '')
        if (cpfDigits.length !== 11) {
            errors.cpf = 'CPF deve conter 11 dígitos.'
            valid = false
        }

        if (!form.birth_date) {
            errors.birth_date = 'Data de nascimento é obrigatória.'
            valid = false
        }

        return valid
    }

    async function onSubmit() {
        if (!validate()) return

        try {
            await auth.submitRegistration({
                name: form.name.trim(),
                cpf: form.cpf.replace(/\D/g, ''),
                birth_date: form.birth_date,
            })
            router.replace({ name: 'users' })
        } catch (e: any) {
            const serverErrors: Record<string, string[]> | undefined = e.response?.data?.errors
            if (serverErrors) {
                for (const [field, msgs] of Object.entries(serverErrors)) {
                    if (field in errors) {
                        errors[field] = msgs[0]
                    }
                }
            }
        }
    }
</script>
