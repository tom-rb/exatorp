// Loading basic libraries
require('./bootstrap');

// A Vue instance to be used as an event bus
window.Events = new Vue();

// Vue global components
Vue.component('confirmation-button', require('./components/confirmation-button.vue'));
Vue.component('modal-dialog', require('./components/modal-dialog.vue'));
Vue.component('cleave-input', require('./components/cleave-input.vue'));

// One use components, for specific views
Vue.component('application-form-view', require('./pages/application-form.vue'));
Vue.component('candidates-dialogs', require('./pages/candidates-dialogs.vue'));
Vue.component('candidates-table', require('./pages/candidates-table.vue'));
Vue.component('member-edit-form', require('./forms/member-edit.vue'));

// Global app Vue instance
const App = new Vue({
    el: '#exato-app',

    data: {
        store: {
            selectionProcess: {
                candidate: {},
                application: {},
            },
        },
        showMobileMenu: false,
        showModal: false,
    },
});
window.App = App;

import setupNoticesHelpers from './notices_helpers';
setupNoticesHelpers(window);
