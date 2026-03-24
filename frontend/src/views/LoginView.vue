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
                <GoogleSignInButton :disabled="auth.loading" @click="auth.redirectToGoogle()" />
            </div>

            <p class="signup-text">
                Não tem conta? <a href="#" class="signup-link">Cadastre-se!</a>
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { useAuthStore } from '@/stores/auth'
    import GoogleSignInButton from '@/components/GoogleSignInButton.vue'

    const auth = useAuthStore()
</script>

<style scoped lang="scss">
    .login-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 16px;
        padding: 2.5rem 2.5rem 2rem;
        width: 100%;
        max-width: 440px;
        text-align: center;
    }

    .login-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 0.25rem;
    }

    .login-subtitle {
        font-size: 0.9rem;
        color: #7c7c8a;
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
        border: 1px solid #dadce0;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.2s;
        color: #202124;

        &::placeholder {
            color: #9aa0a6;
        }

        &:focus {
            border-color: #7c3aed;
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
        color: #5f6368;
        cursor: pointer;

        input {
            accent-color: #7c3aed;
        }
    }

    .forgot-link {
        color: #7c3aed;
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
        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border: none;
        border-radius: 8px;
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
            background: #dadce0;
        }

        span {
            font-size: 0.8rem;
            color: #9aa0a6;
        }
    }

    .social-row {
        display: flex;
        justify-content: center;
    }

    .signup-text {
        margin-top: 1.5rem;
        font-size: 0.8rem;
        color: #7c7c8a;
    }

    .signup-link {
        color: #7c3aed;
        text-decoration: none;
        font-weight: 500;

        &:hover {
            text-decoration: underline;
        }
    }
</style>
