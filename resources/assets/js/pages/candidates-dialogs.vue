<template>
    <div>
        <!--Modal dialog for candidate approval-->
        <modal-dialog v-if="isApprovalVisible" ok-label="Aprovar" @accept="submitApproval"
                      :is-processing="isProcessing" @close="closeModals">
            <p slot="title">Aprovando {{ candidate.name }}</p>

            <p class="is-spaced-bottom-1">
                Você está prestes a aprovar um novo membro e permitir o acesso a esse sistema.
            </p>

            <div class="columns">
                <div class="column">
                    <b-field label="Area para efetivação">
                        <b-select placeholder="Selecione a area" v-model="selectedArea" required expanded
                                  @input="selectedJob = null">
                            <option v-for="area in areas" :value="area" :key="area.id">{{ area.name }}</option>
                        </b-select>
                    </b-field>
                </div>

                <div class="column">
                    <b-field label="Cargo efetivado">
                        <b-select placeholder="Selecione o cargo" v-model="selectedJob" required expanded
                                  ref="jobBox">
                            <option v-for="job in availableJobs" :value="job.id" :key="job.id">{{ job.name }}</option>
                        </b-select>
                    </b-field>
                </div>
            </div>
            <!--<p class="is-spaced-1">Ao clicar em <strong>aprovar</strong> um email de boas vindas será-->
            <!--automaticamente enviado ao candidato informando que foi aceito.</p>-->
            <p slot="footer" v-show="feedback" class="has-text-danger">{{ feedback }}</p>
        </modal-dialog>

        <!--Modal dialog to put a candidate on hold for opportunity-->
        <modal-dialog v-if="isOnHoldVisible" ok-label="Deixar em espera" ok-class="is-info" @accept="sendPatch('hold')"
                      :is-processing="isProcessing" @close="closeModals">
            <p slot="title">Deixando {{ candidate.name }} em espera</p>

            <p class="is-spaced-bottom-1">Você está prestes a marcar o candidato como "em espera". Isso
                significa que a pessoa se enquadra no perfil desejado mas não há mais vagas para ela.</p>

            <p class="is-spaced-bottom-1">Estando em espera, a pessoa será notificada por e-mail quando um próximo
                processo seletivo for aberto. O acesso ao sistema continua restrito.</p>

            <p v-show="application.other_option">Considere também que a pessoa tem uma segunda opção de
                candidatura e que talvez valha a pena
                <strong><a @click="openSwitchIfIdle"> trocar a opção atual. </a></strong>
            </p>

            <p slot="footer" v-show="feedback" class="has-text-danger">{{ feedback }}</p>
        </modal-dialog>

        <!--Modal dialog for rejecting a candidate-->
        <modal-dialog v-if="isRejectionVisible" ok-label="Recusar" ok-class="is-danger" @accept="sendPatch('reject')"
                      :is-processing="isProcessing" @close="closeModals">
            <p slot="title">Recusando {{ candidate.name }}</p>

            <p class="is-spaced-bottom-1">Você está prestes a reprovar um candidato para todas as suas candidaturas.
                Isso apenas deixa registrado que essa pessoa não se enquadrou no perfil da equipe. Ela não receberá
                acesso ao sistema mas poderá se candidatar novamente em uma próxima oportunidade.</p>

            <p v-show="application.other_option">Considere também que a pessoa tem uma segunda opção de
                candidatura e que talvez valha a pena
                <strong><a @click="openSwitchIfIdle"> trocar a opção atual. </a></strong>
            </p>

            <p slot="footer" v-show="feedback" class="has-text-danger">{{ feedback }}</p>
        </modal-dialog>

        <!--Modal dialog for changing a candidate main option for job-->
        <modal-dialog v-if="isOptionSwitchVisible" ok-label="Trocar" ok-class="is-warning" @accept="sendPatch('switch')"
                      :is-processing="isProcessing" @close="closeModals">
            <p slot="title">Trocando opção de {{ candidate.name }}</p>

            <p>Essa ação irá trocar o cargo ao qual a pessoa está aplicando de</p>
            <p class="is-spaced-bottom-1"><em>{{ application.current_option }}</em>
                <b-icon icon="arrow-right" size="is-small"></b-icon> para
                <em>{{ application.other_option }}</em>.</p>

            <p>Caso isso represente uma troca de área, avise a coordenação da nova área.</p>

            <p slot="footer" v-show="feedback" class="has-text-danger">{{ feedback }}</p>
        </modal-dialog>

        <!--Modal dialog for resetting any choice made for the candidate-->
        <modal-dialog v-if="isResetVisible" ok-label="Reset" ok-class="is-dark" @accept="sendPatch('reset')"
                      :is-processing="isProcessing" @close="closeModals">
            <p slot="title">Desfazendo escolha para {{ candidate.name }}</p>

            <p>Essa ação irá <strong>desfazer</strong> qualquer escolha feita para a candidatura dessa pessoa.</p>

            <p slot="footer" v-show="feedback" class="has-text-danger">{{ feedback }}</p>
        </modal-dialog>
    </div>
</template>

<script>
    export default {
        props: ['areas'],

        store: {
            candidate: 'selectionProcess.candidate',
            application: 'selectionProcess.application',
        },

        data() {
            return {
                selectedArea: null,
                selectedJob: null,
                feedback: null,
                isProcessing: false,
                isApprovalVisible: false,
                isOptionSwitchVisible: false,
                isOnHoldVisible: false,
                isRejectionVisible: false,
                isResetVisible: false,
            }
        },

        created() {
            // Listen to events sent by candidates-table to open dialogs
            Events.$on('openApprovalModal', () => {
                this.isApprovalVisible = true;
            });
            Events.$on('openOnHoldModal', () => {
                this.isOnHoldVisible = true;
            });
            Events.$on('openRejectionModal', () => {
                this.isRejectionVisible = true;
            });
            Events.$on('openOptionSwitchModal', () => {
                this.isOptionSwitchVisible = true;
            });
            Events.$on('openResetModal', () => {
                this.isResetVisible = true;
            });
        },

        computed: {
            availableJobs() {
                return (this.selectedArea) ? this.selectedArea.jobs : [];
            }
        },

        methods: {
            submitApproval() {
                if (!this.selectedJob) {
                    this.$refs.jobBox.checkHtml5Validity();
                    return;
                }
                this.sendPatch('approve', { job: this.selectedJob });
            },

            sendPatch(action, data = {}) {
                this.showProcessing();
                const payload = Object.assign({ action }, data);
                axios.patch(this.application.path, payload)
                    .then(this.emitAndClose)
                    .catch(this.showError);
            },

            showProcessing() {
                this.feedback = null;
                this.isProcessing = true;
            },

            showError() {
                this.feedback = "Desculpe, aconteceu um erro. Tente novamente.";
                this.isProcessing = false;
            },

            emitAndClose({data}) {
                Events.$emit('candidateChanged');
                this.isProcessing = false;
                this.closeModals();
                toast(data.message);
            },

            closeModals() {
                this.isApprovalVisible = false;
                this.isOptionSwitchVisible = false;
                this.isOnHoldVisible = false;
                this.isRejectionVisible = false;
                this.isResetVisible = false;
            },

            openSwitchIfIdle() {
                if (! this.isProcessing) {
                    this.closeModals();
                    this.isOptionSwitchVisible = true;
                }
            }
        },
    }
</script>