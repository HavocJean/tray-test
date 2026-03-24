import type { AxiosError } from 'axios'

interface ApiErrorData {
    message?: string
    errors?: Record<string, string[]>
}

export function getApiErrorMessage(error: unknown, fallback: string): string {
    if (error instanceof Error && !('response' in error)) {
        return error.message || fallback
    }
    const axiosErr = error as AxiosError<ApiErrorData>
    return axiosErr?.response?.data?.message ?? fallback
}

export function getValidationErrors(error: unknown): Record<string, string[]> | null {
    const axiosErr = error as AxiosError<ApiErrorData>
    return axiosErr?.response?.data?.errors ?? null
}
