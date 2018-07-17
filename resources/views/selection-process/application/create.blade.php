@extends('layouts.base')
@section('title', trans('auth.registermember'))

@section('main-content')
    <section class="section">
        <div class="container">
            <div class="heading">
                <h1 class="title">Inscrição para o voluntariado</h1>
            </div>
            <p class="subtitle">Cadastre-se no processo de seleção de novos voluntários e voluntárias para o Curso Exato!</p>

            {{-- Inline component used to collapse the jobs radio buttons --}}
            <application-form-view inline-template
                                   first_area_old="{{ old('first_area_id') }}" first_job_old="{{ old('first_area_job') }}"
                                   second_area_old="{{ old('second_area_id') }}" second_job_old="{{ old('second_area_job') }}">

                {!! Form::open() !!}

                @include('form.input', ['id' => 'name', 'label' => 'Nome', 'icon' => 'user',
                                        'placeholder' => 'Nome Completo', 'autofocus' => true])

                @include('form.input', ['id' => 'email', 'label' => 'E-mail', 'type' => 'email',
                                        'placeholder' => 'seu@email.com', 'icon' => 'envelope'])

                @include('form.input', ['id' => 'phones', 'label' => 'Telefone para contato', 'type' => 'tel',
                                        'placeholder' => '19 ... (não esqueça o DDD)', 'icon' => 'phone'])

                <h2 class="title is-4 is-spaced-2">Vínculo com a Unicamp</h2>

                @include('form.input', ['id' => 'ra', 'label' => 'RA', 'type' => 'number',
                                        'placeholder' => 'Registro Acadêmico ou equivalente', 'icon' => 'id-card-o'])

                @include('form.input', ['id' => 'course', 'label' => 'Curso', 'icon' => 'graduation-cap',
                                        'placeholder' => 'Curso ou área de estudo (para não graduando)'])

                @include('form.input', ['id' => 'admission_year', 'label' => 'Ano de Ingresso', 'type' => 'number',
                                        'placeholder' => 'Ano de ingresso', 'icon' => 'calendar'])

                <h2 class="title is-4 is-spaced-2">Área pretendida</h2>

                <p class="content">
                    Escolha a área que gostaria de trabalhar. Dependendo da disponibilidade, você poderá ser alocado para a
                    segunda opção. Há várias atividades dentro da organização do Curso Exato. Caso queira se candidatar a
                    alguma delas, marque aquela que esteja mais interessado/apto a realizar.
                </p>

                <h3 class="title is-5">Primeira opção</h3>
                <p class="subtitle is-6">Selecione uma área para visualizar cargos específicos, se disponíveis.</p>

                <div class="columns is-gapless is-reversed-mobile">
                    <div class="column is-narrow">
                        @foreach($selectionProcess->areas as $area)
                            <div class="field">
                                <b-radio name="first_area_id" v-model="first_area_selected" :native-value="{{ $area->id }}"
                                         :aria-expanded="first_area_selected == {{ $area->id }} ? 'true' : 'false'">
                                    {{ $area->name }}
                                </b-radio>
                            </div>

                            <transition name="collapse">
                                <div v-show="first_area_selected == {{ $area->id }}">
                                    @foreach($selectionProcess->jobsForArea($area) as $job)
                                        <div class="field" style="margin-left: 1.8em; margin-bottom: 0.8em;">
                                            <b-radio name="first_area_job" :native-value="{{ $job->id }}"
                                                     v-model="first_job_selected">
                                                {{ $job->fullDescription }}
                                            </b-radio>
                                        </div>
                                    @endforeach
                                </div>
                            </transition>
                        @endforeach
                    </div>

                    @if ($errors->has('first_area_id') || $errors->has('first_area_job'))
                        <div class="column" style="margin-bottom: 2em;">
                            <b-tooltip type="is-danger" position="is-right" multilined always size="is-large"
                                    label="{{ $errors->first('first_area_id') ?: $errors->first('first_area_job') }}">
                            </b-tooltip>
                        </div>
                    @endif
                </div>

                <h3 class="title is-5">Segunda opção</h3>
                <p class="subtitle is-6">Escolha onde mais poderia contribuir (não obrigatório).</p>

                <div class="columns is-gapless is-reversed-mobile">
                    <div class="column is-narrow">
                        @foreach($selectionProcess->areas as $area)
                            <div class="field">
                                <b-radio name="second_area_id" v-model="second_area_selected" :native-value="{{ $area->id }}"
                                         :aria-expanded="second_area_selected == {{ $area->id }} ? 'true' : 'false'">
                                    {{ $area->name }}
                                </b-radio>
                            </div>

                            <transition name="collapse">
                                <div v-show="second_area_selected == {{ $area->id }}">
                                    @foreach($selectionProcess->jobsForArea($area) as $job)
                                        <div class="field" style="margin-left: 1.8em; margin-bottom: 0.8em;">
                                            <b-radio name="second_area_job" :native-value="{{ $job->id }}"
                                                     v-model="second_job_selected">
                                                {{ $job->fullDescription }}
                                            </b-radio>
                                        </div>
                                    @endforeach
                                </div>
                            </transition>
                        @endforeach
                        <button class="button is-text"
                                v-show="second_area_selected || second_job_selected"
                                @click.prevent="second_area_selected = second_job_selected = null">
                            Remover 2ª opção
                        </button>
                    </div>

                    @if ($errors->has('second_area_id') || $errors->has('second_area_job'))
                        <div class="column" style="margin-bottom: 2em;">
                            <b-tooltip type="is-danger" position="is-right" multilined always size="is-large"
                                    label="{{ $errors->first('second_area_id') ?: $errors->first('second_area_job') }}">
                            </b-tooltip>
                        </div>
                    @endif
                </div>

                <h2 class="title is-4 is-spaced-2">Disponibilidade de horário</h2>

                <div class="content">
                    <p>Cada membro será associado a apenas um dos horários, a depender da distribuição das matérias em
                        relação aos dias da semana. As aulas acontecem normalmente de segunda a quinta-feira, mas há a opção
                        para marcar disponibilidade às sextas-feiras, pois algumas das atividades do projeto (provas, etc.)
                        acontecem às sextas-feiras e precisamos saber quem pode estar presente para acompanhar os alunos.</p>

                    <p>A disponibilidade nesses horários noturnos será menos relevante para quem estiver se candidatando
                        apenas a cargos de organização. Entretanto, algumas atividades organizacionais podem requerer visitas
                        às salas e por isso coletamos esses dados para todos os candidatos.</p>

                    <p><strong>Por favor, marque todos os seus horários disponíveis.</strong></p>

                    @if ($errors->has('availability'))
                        <p class="has-text-centered">
                            <b-tooltip type="is-danger" position="is-top" multilined always
                                       label="{{ $errors->first('availability') }}">
                            </b-tooltip>
                        </p>
                    @endif
                </div>

                @php
                    use \App\Members\LessonAvailability;
                @endphp
                <div class="columns is-mobile is-multiline">
                    @foreach(LessonAvailability::all() as $day => $slots)
                        <div class="column is-half-mobile is-one-third-tablet-only">
                            <p class="notification is-primary availability-header">
                                {{ dayOfWeekName($day) }}
                            </p>
                            <div class="notification availability-cell">
                                @foreach($slots as $slot)
                                    <div class="field">
                                        <b-checkbox name="availability[{{$day}}][{{$slot}}]" :native-value="true"
                                                    value="{{ old("availability.$day.$slot") }}">
                                            {{ LessonAvailability::slotDescription($slot) }}
                                        </b-checkbox>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @include('form.textarea', ['id' => 'availability[obs]', 'label' => 'Observações', 'maxlength' => 350,
                                           'placeholder' => 'Diga se tem certeza sobre seus horários disponíveis ou se eles dependem de algum evento, por exemplo.'])


                <h2 class="title is-4 is-spaced-2">Últimas perguntas para conhecer você.</h2>

                @foreach($selectionProcess->questions as $key => $question)
                    @include('form.textarea', ['id' => "answers[$key]", 'maxlength' => 1000,
                                               'label' => $question->question(),
                                               'placeholder' => $question->helpDescription()])
                @endforeach

                <h3 class="title is-5">Como nos conheceu?</h3>

                @foreach($howDidYouHearOptions as $option)
                    <div class="field">
                        <b-checkbox name="how_did_you_hear[{{ $option->id }}]" :native-value="true"
                                    value="{{ old("how_did_you_hear.$option->id") }}">
                            {{ $option->description }}
                        </b-checkbox>
                    </div>
                @endforeach

                @if ($errors->has('how_did_you_hear*'))
                    <p class="help is-danger">{{ $errors->first('how_did_you_hear*') }}</p>
                @endif

                <h3 class="title is-5 is-spaced-2">Escolha sua senha para acessar o sistema.</h3>

                @include('form.input', ['id' => 'password', 'label' => 'Senha', 'type' => 'password', 'icon' => 'lock',
                                        'placeholder' => 'Digite uma senha'])

                @include('form.input', ['id' => 'password_confirmation', 'label' => 'Confirme a senha', 'type' => 'password',
                                        'placeholder' => 'Confirme a senha', 'icon' => 'check-circle'])

                @include('form.button', ['label' => 'Enviar inscrição', 'icon' => 'chevron-right',
                                         'class' => 'is-primary is-spaced-1'])

                {!! Form::close() !!}
            </application-form-view>
        </div>
    </section>
@endsection