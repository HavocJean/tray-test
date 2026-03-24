export function formatCpf(value: string | null): string {
    if (!value) return '—'
    const d = value.replace(/\D/g, '')
    if (d.length !== 11) return value
    return `${d.slice(0, 3)}.${d.slice(3, 6)}.${d.slice(6, 9)}-${d.slice(9)}`
}

export function formatCpfInput(value: string): string {
    const digits = value.replace(/\D/g, '').slice(0, 11)
    if (digits.length <= 3) return digits
    if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`
    if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`
    return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`
}

export function formatDate(value: string | null): string {
    if (!value) return '—'
    const [y, m, d] = value.split('-')
    return `${d}/${m}/${y}`
}
