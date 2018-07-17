<template>
    <form @submit.prevent="onSubmit">

        <section class="vsection">
            <h2 class="title is-4">Contatos</h2>

            <b-field label="Nome"
                     :type="fieldType('name')"
                     :message="errorMessage('name')">
                <b-input v-model="form.name" maxlength="155" :has-counter="false"
                         @input="clearError('name')"></b-input>
            </b-field>

            <b-field label="E-mail"
                     :type="fieldType('email')"
                     :message="errorMessage('email')">
                <b-input v-model="form.email" maxlength="255" :has-counter="false"
                         @input="clearError('email')"></b-input>
            </b-field>

            <b-field label="Telefones"
                     :type="fieldType('phones')"
                     :message="errorMessage('phones')">
                <phone-list-input v-model="form.phones">
                </phone-list-input>
            </b-field>
        </section>

        <section class="vsection">
            <h2 class="title is-4">Vínculo</h2>

            <b-field label="RA"
                     :type="fieldType('ra')"
                     :message="errorMessage('ra')">
                <b-input v-model="form.ra" type="number"
                         @input="clearError('ra')"></b-input>
            </b-field>

            <b-field grouped>
                <b-field label="Ano de ingresso"
                         :type="fieldType('admission_year')"
                         :message="errorMessage('admission_year')">
                    <b-input v-model="form.admission_year" type="number"
                             :min="1966"
                             :max="(new Date()).getFullYear()"
                             @input="clearError('admission_year')"></b-input>
                </b-field>

                <b-field label="Curso" expanded
                         :type="fieldType('course')"
                         :message="errorMessage('course')">
                    <b-input v-model="form.course" maxlength="155" :has-counter="false"
                             @input="clearError('course')"></b-input>
                </b-field>
            </b-field>
        </section>

        <section class="vsection">
            <a class="button is-black is-outlined" @click="isPasswordOpen = !isPasswordOpen">
                <span>Trocar a senha</span>
                <b-icon :icon="isPasswordOpen ? 'angle-up' : 'angle-down' "></b-icon>
            </a>

            <transition name="collapse">
                <div class="card" v-show="isPasswordOpen">
                    <div class="card-content">
                        <b-field horizontal label="Senha atual"
                                 :type="fieldType('current_password')"
                                 :message="errorMessage('current_password')">
                            <b-input v-model="form.current_password"
                                     type="password"
                                     @input="clearError('current_password')"
                                     password-reveal>
                            </b-input>
                        </b-field>

                        <b-field horizontal label="Nova senha"
                                 :type="fieldType('password')"
                                 :message="errorMessage('password')">
                            <b-input v-model="form.password"
                                     type="password"
                                     @input="clearError('password')"
                                     password-reveal>
                            </b-input>
                        </b-field>
                    </div>
                </div>
            </transition>

            <div class="is-spaced-1">
                <button class="button is-success"
                        :class="{'is-loading': form.isLoading()}">
                    Atualizar
                </button>
            </div>
        </section>
    </form>
</template>

<script>
    import Form, {FormMixin} from './form';
    import PhoneListInput from "../components/phone-list-input";

    export default {
        components: {PhoneListInput},
        mixins: [FormMixin],

        props: {
            member: {
                type: Object
            },
        },

        data() {
            return {
                form: new Form({
                    name: this.member.name,
                    email: this.member.email,
                    phones: this.member.phones,
                    ra: this.member.ra,
                    course: this.member.course,
                    admission_year: this.member.admission_year,
                    current_password: '',
                    password: ''
                }),
                isPasswordOpen: false,
            }
        },

        methods: {
            onSubmit () {
                let redirectTo = this.member.path;
                this.form.patch(this.member.path)
                    .then(() => location.assign(redirectTo))
                    .catch(() => {/* because the console screams */});
            },
        },
    }
</script>
