import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';


export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(urlToCheck, currentUrl) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href) {
    if (!href) return null;
    return typeof href === 'string' ? href : href.url;
}
