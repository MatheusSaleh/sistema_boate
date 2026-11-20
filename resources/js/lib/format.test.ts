import { describe, expect, it } from 'vite-plus/test';
import {
    formatDate,
    formatDateTime,
    formatMoney,
    parseDecimal,
} from './format';

// Intl separa "R$" do valor com espaço não separável (U+00A0)
const normalize = (text: string) => text.replace(/ /g, ' ');

describe('formatMoney', () => {
    it('formata em reais', () => {
        expect(normalize(formatMoney(1234.56))).toBe('R$ 1.234,56');
        expect(normalize(formatMoney(0))).toBe('R$ 0,00');
        expect(normalize(formatMoney(1000000))).toBe('R$ 1.000.000,00');
        expect(normalize(formatMoney(-5.5))).toBe('-R$ 5,50');
    });

    it('retorna vazio sem valor', () => {
        expect(formatMoney(null)).toBe('');
        expect(formatMoney(undefined)).toBe('');
        expect(formatMoney(Number.NaN)).toBe('');
    });
});

describe('formatDate', () => {
    it('formata como dd/mm/aaaa no fuso de São Paulo', () => {
        // 02:30 UTC ainda é dia 19 em São Paulo (23:30)
        expect(formatDate('2026-11-20T02:30:00Z')).toBe('19/11/2026');
        expect(formatDate(new Date('2026-01-05T15:00:00Z'))).toBe('05/01/2026');
    });

    it('não desloca datas sem horário', () => {
        expect(formatDate('2026-11-19')).toBe('19/11/2026');
    });

    it('retorna vazio para entrada vazia ou inválida', () => {
        expect(formatDate(null)).toBe('');
        expect(formatDate('')).toBe('');
        expect(formatDate('não é data')).toBe('');
    });
});

describe('formatDateTime', () => {
    it('formata em 24h no fuso de São Paulo', () => {
        expect(formatDateTime('2026-11-20T02:05:00Z')).toBe('19/11/2026 23:05');
        expect(formatDateTime('2026-11-19T03:00:00Z')).toBe('19/11/2026 00:00');
    });

    it('retorna vazio para entrada vazia ou inválida', () => {
        expect(formatDateTime(undefined)).toBe('');
        expect(formatDateTime('xyz')).toBe('');
    });
});

describe('parseDecimal', () => {
    it.each([
        ['1.234,56', 1234.56],
        ['1234,56', 1234.56],
        ['12,5', 12.5],
        ['0,99', 0.99],
        ['R$ 1.234,56', 1234.56],
        ['  10 ', 10],
        ['1.234', 1234],
        ['1.234.567', 1234567],
        ['12.5', 12.5],
        ['1234.56', 1234.56],
        ['-3,20', -3.2],
        ['-R$ 7,00', -7],
    ])('"%s" → %s', (input, expected) => {
        expect(parseDecimal(input)).toBe(expected);
    });

    it.each(['', '   ', 'abc', '1,2,3', '12,', ',5', '-', null, undefined])(
        'retorna null para %j',
        (input) => {
            expect(parseDecimal(input)).toBeNull();
        },
    );
});
