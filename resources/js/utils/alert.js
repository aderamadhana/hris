// src/Utils/alert.js
import { reactive } from 'vue';

export const alerts = reactive([]);

let counter = 0;

export function triggerAlert(type = 'info', message = '', timeout = 3000) {
    const id = ++counter;

    alerts.push({
        id,
        type,      // 'success' | 'warning' | 'error' | 'info'
        message,
    });

    if (timeout && timeout > 0) {
        setTimeout(() => {
            closeAlert(id);
        }, timeout);
    }
}

export function closeAlert(id) {
    const idx = alerts.findIndex((a) => a.id === id);
    if (idx !== -1) alerts.splice(idx, 1);
}
