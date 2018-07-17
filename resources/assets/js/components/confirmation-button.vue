<template>
    <button class="button" :class="typeClass" @click="callDialog">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        name: "confirmation-button",
        props: {
            title: {
                type: String,
                default: ''
            },
            message: {
                type: String,
                default: 'Confirma?'
            },
            type: {
                type: String,
                default: 'primary'
            },
            okLabel: {
                type: String,
                default: 'Confirmar'
            },
            cancelLabel: {
                type: String,
                default: 'Cancelar'
            },
            autofocusCancel: {
                type: Boolean,
                default: false
            },
            route: {
                type: String,
                default: ''
            },
            method: {
                type: String,
                default: 'get'
            },
            successAction: {
                type: String,
                default: 'reload'
            },
        },

        computed: {
            typeClass() {
                return 'is-' + this.type;
            },

            hasIcon() {
                return ['success', 'info', 'warning', 'danger'].includes(this.type);
            },

            focusOn() {
                return this.autofocusCancel ? 'cancel' : 'confirm';
            },
        },

        data() {
            return {
                loading: null,
            }
        },

        methods: {
            callDialog() {
                this.$dialog.confirm({
                    title: this.title,
                    message: this.message,
                    type: this.typeClass,
                    confirmText: this.okLabel,
                    hasIcon: this.hasIcon,
                    focusOn: this.focusOn,
                    onConfirm: this.onConfirm,
                    onCancel: () => toastQuick('Nada feito'),
                    scroll: 'keep',
                });
            },

            onConfirm() {
                this.loading = this.$loading.open();

                axios[this.method](this.route, {})
                    .then(response => this.onResponse(response))
                    .catch(error => this.onError(error.response));
            },

            onResponse(response) {
                let data = response.data;

                if (data.error)
                    return this.onError(response);

                if (this.successAction === 'reload')
                    location.reload();
            },

            onError(response = null) {
                this.closeLoading();

                let message = 'Erro, por favor recarregue a página';
                if (response && response.data && response.data.error)
                    message += ' - ' + response.data.error;
                message += '.';

                toastDanger(message);
            },

            closeLoading() {
                if (this.loading)
                    this.loading.close();
            }
        },
    }
</script>
