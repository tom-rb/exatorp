<template>
    <div class="control"
         :class="[{'is-expanded': expanded}]">
        <input ref="input"
               type="text"
               class="input"/>
    </div>
</template>

<script>
    // https://github.com/vue-bulma/cleave/blob/master/src/Cleave.vue
    require('cleave.js/dist/addons/cleave-phone.br');
    import Cleave from 'cleave.js';

    export default {
        name: "PhoneInput",

        props: {
            value: {
                type: String,
                default: ''
            },
            expanded: Boolean,
            options: {
                type: Object,
                default: () => ({
                    phone: true,
                    phoneRegionCode: 'BR',
                })
            },
            events: {
                type: Object,
                default: () => ({})
            }
        },

        data () {
            return {
                cleave: null
            }
        },

        methods: {
            emitEvent () {
                this.$emit('input', this.$refs.input.value);
                this.$emit('rawValueChanged', this.cleave.getRawValue());
            }
        },

        mounted () {
            this.$refs.input.value = this.value;
            this.cleave = new Cleave(this.$refs.input, this.options);
            Object.keys(this.events).map((key) => {
                this.$refs.input.addEventListener(key, this.events[key])
            });
            if (this.options.maxLength) {
                this.$refs.input.setAttribute('maxlength', this.options.maxLength)
            }

            // in case of cleave.js remove result or properties from cleave instance.
            if (this.cleave.properties && this.cleave.properties.hasOwnProperty('result')) {
                this.$watch('cleave.properties.result', this.emitEvent)
            } else {
                this.$el.addEventListener('input', this.emitEvent)
            }
        },

        beforeDestroy () {
            this.cleave.destroy()
        },

        watch: {
            options: {
                deep: true,
                handler (val) {
                    this.cleave.destroy();
                    this.cleave = new Cleave(this.$refs.input, val)
                }
            }
        },

    }
</script>