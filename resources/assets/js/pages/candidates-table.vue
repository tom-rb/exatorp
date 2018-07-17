<template>
    <b-table
            :data="applications"
            :loading="loading"
            paginated
            backend-pagination
            :total="pagination.total"
            :current-page="pagination.current_page"
            :per-page="pagination.per_page"
            @page-change="fetch">

        <template slot-scope="props" slot="header">
            <b-tooltip size="is-small" type="is-dark" dashed multilined
                       :active="!!props.column.meta" :label="props.column.meta || ''">
                {{ props.column.label }}
            </b-tooltip>
        </template>

        <template slot-scope="props">
            <b-table-column label="Nome">
                <a :href="props.row.path" v-text="props.row.candidate.name"></a>
            </b-table-column>

            <b-table-column label="Curso">
                {{ props.row.candidate.course }}
            </b-table-column>

            <b-table-column label="Candidatura atual">
                {{ props.row.current_option }}
                <div v-if="props.row.other_option">
                    <small>
                        ({{ props.row.trying_first_option
                            ? '2ª opção é' : '1ª opção foi' }}: {{ props.row.other_option }})
                    </small>
                </div>
            </b-table-column>

            <b-table-column label="Ações" meta="As ações requerem confirmação. Clique e veja!">
                <div v-if="props.row.status">
                    <div v-if="props.row.status === STATUS.APPROVED" class="button is-small" disabled>
                        <span class="icon is-small"><i class="fa fa-check-circle"></i></span>
                        <span>Aprovado</span>
                    </div>

                    <div v-else-if="props.row.status === STATUS.ON_HOLD" class="button is-small" disabled>
                        <span class="icon is-small"><i class="fa fa-clock-o"></i></span>
                        <span>Em espera</span>
                    </div>

                    <div v-else-if="props.row.status === STATUS.REJECTED" class="button is-small" disabled>
                        <span class="icon is-small"><i class="fa fa-remove"></i></span>
                        <span>Não aprovado</span>
                    </div>

                    <b-tooltip size="is-small" type="is-dark" label="Desfazer escolha">
                        <a class="button is-small is-outlined" v-if="canReset"
                           @click="openResetModal(props.row)">
                            <span class="icon is-small"><i class="fa fa-undo"></i></span>
                        </a>
                    </b-tooltip>
                </div>

                <div v-else-if="canApprove">
                    <div class="row">
                        <b-tooltip size="is-small" type="is-dark" label="Aprovar">
                            <a class="button is-small is-success is-outlined" v-if="props.row.current_area_slug == currentArea"
                               @click="openApprovalModal(props.row)">
                                <span class="icon is-small"><i class="fa fa-check"></i></span>
                            </a>
                        </b-tooltip>

                        <b-tooltip size="is-small" type="is-dark" label="Deixar em espera">
                            <a class="button is-small is-info is-outlined" v-if="props.row.current_area_slug == currentArea"
                                @click="openOnHoldModal(props.row)">
                                <span class="icon is-small"><i class="fa fa-hourglass-half"></i></span>
                            </a>
                        </b-tooltip>

                        <b-tooltip size="is-small" type="is-dark" label="Reprovar">
                            <a class="button is-small is-danger is-outlined" v-if="props.row.current_area_slug == currentArea"
                                @click="openRejectionModal(props.row)">
                                <span class="icon is-small"><i class="fa fa-remove"></i></span>
                            </a>
                        </b-tooltip>
                    </div>

                    <div class="row" v-if="props.row.other_option">
                        <b-tooltip size="is-small" type="is-dark" label="Trocar opção">
                            <a class="button is-small is-warning is-outlined"
                               @click="openOptionSwitchModal(props.row)">
                                <span class="icon is-small"><i class="fa fa-exchange"></i></span>
                            </a>
                        </b-tooltip>
                    </div>
                </div>

                <div v-else> - </div>
            </b-table-column>
        </template>

        <template slot="empty">
            Sem entradas por enquanto.
        </template>
    </b-table>
</template>

<script>
    import MemberApplication from '../config/member-application';

    export default {
        props: ['candidatesPath', 'canApprove', 'canReset'],

        store: ['selectionProcess'],

        data() {
            return {
                pagination: {},
                applications: [],
                loading: true,
                STATUS: MemberApplication.STATUS,
            }
        },

        created() {
            // Listen to event sent by candidates-dialogs to refresh the table
            Events.$on('candidateChanged', () => {
                this.fetch(this.pagination.current_page)
            });
        },

        mounted() {
            this.fetch();
        },

        computed: {
            currentArea() {
                return URI.parseQuery(location.search).area;
            }
        },

        methods: {
            fetch(page) {
                this.loading = true;
                axios.get(this.pagePath(page))
                    .then(this.updateView);
            },

            pagePath(page = 1) {
                let uri = URI(this.candidatesPath).addSearch('page', page);
                if (this.currentArea) uri.addSearch('area', this.currentArea);

                return uri.toString();
            },

            updateView({data}) {
                this.loading = false;
                this.pagination = data;
                this.applications = data.data;
            },

            openApprovalModal(row)
            {
                this.setSelectedCandidate(row);
                Events.$emit('openApprovalModal');
            },

            openOnHoldModal(row)
            {
                this.setSelectedCandidate(row);
                Events.$emit('openOnHoldModal');
            },

            openRejectionModal(row)
            {
                this.setSelectedCandidate(row);
                Events.$emit('openRejectionModal');
            },

            openOptionSwitchModal(row)
            {
                this.setSelectedCandidate(row);
                Events.$emit('openOptionSwitchModal');
            },

            openResetModal(row)
            {
                this.setSelectedCandidate(row);
                Events.$emit('openResetModal');
            },

            /**
             * Share the current selected candidate.
             */
            setSelectedCandidate(row)
            {
                this.selectionProcess.application = row;
                this.selectionProcess.candidate = row.candidate;
            }
        }
    }
</script>