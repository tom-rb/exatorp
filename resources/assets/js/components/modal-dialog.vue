<template>
    <div class="modal is-active">
        <div class="modal-background" @click="reject"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">
                    <slot name="title"></slot>
                </p>
            </header>
            <section class="modal-card-body">
                <slot></slot>
            </section>
            <footer class="modal-card-foot">
                <a class="button" :class="[{ 'is-loading': isProcessing }, okClass]" @click="accept">{{ okLabel }}</a>
                <a class="button" @click="reject" :disabled="isProcessing">{{ cancelLabel }}</a>
                <slot name="footer"></slot>
            </footer>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            okLabel: {
                type: String,
                default: 'Confirmar'
            },
            okClass: {
                type: String,
                default: 'is-success'
            },
            cancelLabel: {
                type: String,
                default: 'Cancelar'
            },
            isProcessing: false,
        },

        methods: {
            accept() {
                this.$emit('accept');
            },

            reject() {
                if (! this.isProcessing)
                    this.$emit('close');
            }
        }
    }
</script>