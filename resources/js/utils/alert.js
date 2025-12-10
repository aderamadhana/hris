import { reactive } from 'vue';

export const alerts = reactive([]);

let counter = 0;

/**
 * Text alert (default)
 */
export function triggerAlert(
    type = 'info',
    message = '',
    timeout = 15000
) {
    const id = ++counter;

    alerts.push({
        id,
        type,
        message,
        isHtml: false,
    });

    if (timeout > 0) {
        setTimeout(() => closeAlert(id), timeout);
    }
}

export function closeAlert(id) {
    const idx = alerts.findIndex(a => a.id === id);
    if (idx !== -1) alerts.splice(idx, 1);
}