<template>
    <div class="page-center">
        <div class="login-card">
            <h1 class="login-title">Test Tray</h1>
            <p class="login-subtitle">Faça login para continuar</p>

            <div v-if="auth.error" class="alert-error">{{ auth.error }}</div>

            <form class="login-form" @submit.prevent>
                <input type="text" placeholder="E-mail" class="login-input" />
                <input type="password" placeholder="Senha" class="login-input" />

                <div class="login-options">
                    <label class="remember-me">
                        <input type="checkbox" />
                        <span>Lembre-me</span>
                    </label>
                    <a href="#" class="forgot-link">Esqueceu a senha?</a>
                </div>

                <button type="button" class="btn-signin">Entrar</button>
            </form>

            <div class="divider">
                <span>Ou</span>
            </div>

            <div class="social-row">
                <GoogleSignInButton :disabled="auth.loading" @click="handleGoogleLogin" />
            </div>

            <p class="signup-text">
                Não tem conta? <a href="#" class="signup-link">Cadastre-se!</a>
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'
    import GoogleSignInButton from '@/components/GoogleSignInButton.vue'

    const auth = useAuthStore()
    const router = useRouter()

    async function handleGoogleLogin() {
        await auth.loginWithGoogle()
        if (auth.isAuthenticated) {
            router.push({ name: 'register' })
        }
    }
</script>

<style scoped lang="scss">
    .login-card {
        background: $color-white;
        border: 1px solid $color-border-light;
        border-radius: $border-radius-lg;
        padding: 2.5rem 2.5rem 2rem;
        width: 100%;
        max-width: 440px;
        text-align: center;
    }

    .login-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: $color-heading;
        margin-bottom: 0.25rem;
    }

    .login-subtitle {
        font-size: 0.9rem;
        color: $color-muted;
        margin-bottom: 2rem;
    }

    .login-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .login-input {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        border: 1px solid $color-border;
        border-radius: $border-radius;
        outline: none;
        transition: border-color 0.2s;
        color: $color-text;

        &::placeholder {
            color: $color-placeholder;
        }

        &:focus {
            border-color: $color-accent;
        }
    }

    .login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        color: $color-text-secondary;
        cursor: pointer;

        input {
            accent-color: $color-accent;
        }
    }

    .forgot-link {
        color: $color-accent;
        text-decoration: none;
        font-weight: 500;

        &:hover {
            text-decoration: underline;
        }
    }

    .btn-signin {
        width: 100%;
        padding: 0.75rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: $color-white;
        background: linear-gradient(135deg, $color-accent, $color-accent-light);
        border: none;
        border-radius: $border-radius;
        cursor: pointer;
        transition: opacity 0.2s;
        margin-top: 0.25rem;

        &:hover {
            opacity: 0.9;
        }
    }

    .divider {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
        gap: 1rem;

        &::before,
        &::after {
            content: '';
            flex: 1;
            height: 1px;
            background: $color-border;
        }

        span {
            font-size: 0.8rem;
            color: $color-placeholder;
        }
    }

    .social-row {
        display: flex;
        justify-content: center;
    }

    .signup-text {
        margin-top: 1.5rem;
        font-size: 0.8rem;
        color: $color-muted;
    }

    .signup-link {
        color: $color-accent;
        text-decoration: none;
        font-weight: 500;

        &:hover {
            text-decoration: underline;
        }
    }
</style>