import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import PaginationControls from '@/components/PaginationControls.vue'
import type { PaginationMeta } from '@/types/user'

const baseMeta: PaginationMeta = {
    current_page: 2,
    last_page: 10,
    per_page: 20,
    from: 21,
    to: 40,
    total: 200,
}

describe('PaginationControls', () => {
    it('mostra botões de página e total de registros', () => {
        const wrapper = mount(PaginationControls, { props: { meta: baseMeta } })
        expect(wrapper.text()).toContain('200 resultados')
        const pageButtons = wrapper.findAll('.page-btn:not(.page-arrow)')
        expect(pageButtons.length).toBeGreaterThan(0)
    })

    it('destaca a página ativa', () => {
        const wrapper = mount(PaginationControls, { props: { meta: baseMeta } })
        const active = wrapper.find('.page-btn.active')
        expect(active.exists()).toBe(true)
        expect(active.text()).toBe('2')
    })

    it('emite page ao clicar em número de página', async () => {
        const wrapper = mount(PaginationControls, { props: { meta: baseMeta } })
        const pageButtons = wrapper.findAll('.page-btn:not(.page-arrow)')
        const page3 = pageButtons.find(b => b.text() === '3')
        expect(page3).toBeDefined()
        await page3!.trigger('click')
        expect(wrapper.emitted('page')![0]).toEqual([3])
    })

    it('seta anterior emite página - 1', async () => {
        const wrapper = mount(PaginationControls, { props: { meta: baseMeta } })
        const arrows = wrapper.findAll('.page-arrow')
        await arrows[0].trigger('click')
        expect(wrapper.emitted('page')![0]).toEqual([1])
    })

    it('seta próxima emite página + 1', async () => {
        const wrapper = mount(PaginationControls, { props: { meta: baseMeta } })
        const arrows = wrapper.findAll('.page-arrow')
        await arrows[1].trigger('click')
        expect(wrapper.emitted('page')![0]).toEqual([3])
    })

    it('desabilita seta anterior na primeira página', () => {
        const meta = { ...baseMeta, current_page: 1 }
        const wrapper = mount(PaginationControls, { props: { meta } })
        const prevArrow = wrapper.findAll('.page-arrow')[0]
        expect(prevArrow.attributes('disabled')).toBeDefined()
    })

    it('desabilita seta próxima na última página', () => {
        const meta = { ...baseMeta, current_page: 10 }
        const wrapper = mount(PaginationControls, { props: { meta } })
        const nextArrow = wrapper.findAll('.page-arrow')[1]
        expect(nextArrow.attributes('disabled')).toBeDefined()
    })

    it('não mostra botões se só tem 1 página', () => {
        const meta = { ...baseMeta, last_page: 1 }
        const wrapper = mount(PaginationControls, { props: { meta } })
        expect(wrapper.findAll('.page-btn')).toHaveLength(0)
    })

    it('mostra reticências quando há muitas páginas', () => {
        const meta = { ...baseMeta, current_page: 5, last_page: 15 }
        const wrapper = mount(PaginationControls, { props: { meta } })
        expect(wrapper.text()).toContain('...')
    })
})
