import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import UserTable from '@/components/UserTable.vue'
import type { User } from '@/types/user'

const users: User[] = [
    {
        uuid: '1',
        name: 'João Souza',
        email: 'joao@mail.com',
        cpf: '12345678901',
        birth_date: '1995-08-20',
        created_at: '2026-01-01T00:00:00Z',
    },
    {
        uuid: '2',
        name: null,
        email: null,
        cpf: null,
        birth_date: null,
        created_at: '2026-01-02T00:00:00Z',
    },
]

describe('UserTable', () => {
    it('renderiza linhas para cada usuário', () => {
        const wrapper = mount(UserTable, { props: { users } })
        const rows = wrapper.findAll('tbody tr')
        expect(rows).toHaveLength(2)
    })

    it('exibe dados formatados corretamente', () => {
        const wrapper = mount(UserTable, { props: { users } })
        const firstRow = wrapper.findAll('tbody tr')[0]
        const cells = firstRow.findAll('td')

        expect(cells[0].text()).toBe('João Souza')
        expect(cells[1].text()).toBe('joao@mail.com')
        expect(cells[2].text()).toBe('123.456.789-01')
        expect(cells[3].text()).toBe('20/08/1995')
    })

    it('exibe "—" para campos nulos', () => {
        const wrapper = mount(UserTable, { props: { users } })
        const secondRow = wrapper.findAll('tbody tr')[1]
        const cells = secondRow.findAll('td')

        expect(cells[0].text()).toBe('—')
        expect(cells[1].text()).toBe('—')
        expect(cells[2].text()).toBe('—')
        expect(cells[3].text()).toBe('—')
    })

    it('mostra mensagem quando lista vazia', () => {
        const wrapper = mount(UserTable, { props: { users: [] } })
        expect(wrapper.text()).toContain('Nenhum usuário encontrado')
    })
})
