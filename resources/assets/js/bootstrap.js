/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components.
 */
window.Vue = require('vue');

/**
 * Vue-stash is a Vue.js plugin that makes it easy to share
 * reactive data between components.
 */
import VueStash from 'vue-stash';
Vue.use(VueStash);

/**
 * Buefy, a lightweight UI components for Vue.js based on Bulma.
 */
import Buefy from 'buefy';
Vue.use(Buefy, {
    defaultIconPack: 'fa',
    defaultDialogCancelText: 'Cancelar',
    defaultToastDuration: 3000,
});

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */
// let token = document.head.querySelector('meta[name="csrf-token"]');
//
// if (token) {
//     window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
// } else {
//     console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
// }

/**
 * Simple URI parser/mounter
 */
window.URI = require('urijs');
