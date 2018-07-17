<template>
    <div>
        <b-field v-for="(entry, index) in phones" :key="entry.id">
            <phone-input expanded
                         @input="emitModelUpdate"
                         v-model="entry.phone"
            ></phone-input>
            <p class="control" v-if="entry.id > 0">
                <a class="button is-danger"
                        @click="removePhone(index)">
                    <span class="icon is-small">
                        <i class="fa fa-trash-o"></i>
                    </span>
                </a>
            </p>
            <p class="control" v-else>
                <a class="button is-success"
                        @click="addPhone">
                    <span class="icon is-small">
                        <i class="fa fa-plus"></i>
                    </span>
                </a>
            </p>
        </b-field>
    </div>
</template>

<script>
    import PhoneInput from "../components/phone-input";

    export default {
        name: "PhoneListInput",
        components: {PhoneInput},

        props: {
            value: {
                type: Array,
                default: () => ([])
            },
            max: {
                type: Number,
                default: 4,
            }
        },

        data() {
            return {
                phones: [],
            }
        },

        methods: {
            newPhone(id, phone = '') {
                return { id, phone };
            },

            addPhone() {
                if (this.phones.length >= this.max)
                    return;

                let newId = 1 + this.phones[this.phones.length - 1].id;

                this.phones.push(this.newPhone(newId));
                this.emitModelUpdate();
            },

            removePhone(index) {
                this.phones.splice(index, 1);
                this.emitModelUpdate();
            },

            emitModelUpdate() {
                // Convert array of objects to raw array of phones
                let phoneArray = [];
                for (let i = 0; i < this.phones.length; i += 1)
                    phoneArray[i] = this.phones[i].phone;

                this.$emit('input', phoneArray);
            },
        },

        created() {
            if (this.value) {
                // Convert raw array of phones to traceable objects for vue v-for
                for (let i = 0; i < this.value.length; i += 1)
                    this.phones[i] = this.newPhone(i, this.value[i]);
            }
            else {
                // Empty initial values
                this.phones[0] = this.newPhone(0);
                this.emitModelUpdate();
            }
        },
    }
</script>
