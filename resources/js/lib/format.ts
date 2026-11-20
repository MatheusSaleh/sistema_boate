export const LOCALE = 'pt-BR';
export const TIME_ZONE = 'America/Sao_Paulo';

type DateInput = Date | string | number;

const moneyFormatter = new Intl.NumberFormat(LOCALE, {
    style: 'currency',
    currency: 'BRL',
});

const dateTimeFormatter = new Intl.DateTimeFormat(LOCALE, {
    timeZone: TIME_ZONE,
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hourCycle: 'h23',
});

const DATE_ONLY = /^(\d{4})-(\d{2})-(\d{2})$/;

/** 1234.56 → "R$ 1.234,56" */
export function formatMoney(value: number | null | undefined): string {
    if (value === null || value === undefined || Number.isNaN(value)) {
        return '';
    }

    return moneyFormatter.format(value);
}

function toParts(value: DateInput): Record<string, string> | null {
    const date = value instanceof Date ? value : new Date(value);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return Object.fromEntries(
        dateTimeFormatter
            .formatToParts(date)
            .map((part) => [part.type, part.value]),
    );
}

/**
 * → "19/11/2026", no fuso de São Paulo.
 * Datas sem horário ("2026-11-19") são mantidas como estão, sem conversão de fuso.
 */
export function formatDate(value: DateInput | null | undefined): string {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const dateOnly = typeof value === 'string' ? DATE_ONLY.exec(value) : null;

    if (dateOnly) {
        const [, year, month, day] = dateOnly;

        return `${day}/${month}/${year}`;
    }

    const parts = toParts(value);

    return parts ? `${parts.day}/${parts.month}/${parts.year}` : '';
}

/** → "19/11/2026 23:05" (24h), no fuso de São Paulo. */
export function formatDateTime(value: DateInput | null | undefined): string {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const parts = toParts(value);

    return parts
        ? `${parts.day}/${parts.month}/${parts.year} ${parts.hour}:${parts.minute}`
        : '';
}

const THOUSANDS_ONLY = /^\d{1,3}(\.\d{3})+$/;

/**
 * Converte um valor digitado pelo usuário em número, aceitando vírgula decimal.
 *
 * "1.234,56" → 1234.56 · "12,5" → 12.5 · "R$ 10" → 10 · "1.234" → 1234
 * "12.5" → 12.5 (ponto como decimal quando não parece separador de milhar)
 * Retorna null para entrada vazia ou inválida.
 */
export function parseDecimal(input: string | null | undefined): number | null {
    if (input === null || input === undefined) {
        return null;
    }

    let text = input.replace(/R\$/gi, '').replace(/\s/g, '');

    const negative = text.startsWith('-');

    if (negative) {
        text = text.slice(1);
    }

    if (text === '') {
        return null;
    }

    if (text.includes(',')) {
        text = text.replace(/\./g, '').replace(',', '.');
    } else if (THOUSANDS_ONLY.test(text)) {
        text = text.replace(/\./g, '');
    }

    if (!/^\d+(\.\d+)?$/.test(text)) {
        return null;
    }

    const value = Number(text);

    return negative ? -value : value;
}
