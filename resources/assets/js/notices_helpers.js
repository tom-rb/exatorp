/**
 * Toast a message in the current DOM.
 *
 * @param params|string
 * @param type
 * @param overrides
 */
function toast(params, type = 'is-dark', overrides) {
    let message;
    if (typeof params === 'string') message = params;

    const defaultParam = { message, type, position: 'is-bottom'};

    const propsData = Object.assign(defaultParam, params, overrides);
    window.App.$toast.open(propsData);
}

/**
 * Setup function. Assumes a window.App Vue instance exists
 * with a registered Buefy Toast component.
 *
 * @param window the window object with App
 */
export default function (window) {
    window.toast = toast;
    window.toastSuccess = (params) => toast(params, 'is-success');
    window.toastInfo = (params) => toast(params, 'is-info');
    window.toastWarning = (params) => toast(params, 'is-warning');
    window.toastDanger = (params) => toast(params, 'is-danger');
    window.toastQuick = (params) => toast(params, 'is-dark', {duration: 1200});

    window.snackbar = window.App.$snackbar.open;
}