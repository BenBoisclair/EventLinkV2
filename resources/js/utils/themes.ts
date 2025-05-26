import type { ThemeColors, ThemeOptions } from '@/types/websiteBuilder';

export const createDefaultThemes = (): Record<ThemeOptions, ThemeColors> => ({
    grey: {
        primary: '#343A40',
        secondary: '#CED4DA',
        light: '#F8F9FA',
        dark: '#212529',
    },
    blue: {
        primary: '#1864AB',
        secondary: '#339AF0',
        light: '#E7F5FF',
        dark: '#1971C2',
    },
    green: {
        primary: '#2B8A3E',
        secondary: '#40C057',
        light: '#EBFBEE',
        dark: '#2F9E44',
    },
    mango: {
        primary: '#E67700',
        secondary: '#FFA94D',
        light: '#FFF4E6',
        dark: '#F76707',
    },
    corporate: {
        primary: '#2C3E50',
        secondary: '#34495E',
        light: '#ECF0F1',
        dark: '#2C3E50',
    },
    festive: {
        primary: '#E74C3C',
        secondary: '#C0392B',
        light: '#FADBD8',
        dark: '#C0392B',
    },
    elegant: {
        primary: '#6C5CE7',
        secondary: '#5D4EC9',
        light: '#E6E6FA',
        dark: '#5D4EC9',
    },
    modern: {
        primary: '#00B894',
        secondary: '#00A884',
        light: '#D1F2EB',
        dark: '#00A884',
    },
    custom: {
        primary: '#000000',
        secondary: '#000000',
        light: '#FFFFFF',
        dark: '#000000',
    },
});
