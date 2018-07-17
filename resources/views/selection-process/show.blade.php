@extends('layouts.member.base')
@section('title', 'Processos Seletivos - Curso Exato')

@section('app-content')
    <section class="section">
        <h1 class="heading title is-4">Processo Seletivo</h1>
        <h2 class="subtitle is-6">Cuidando da continuidade do nosso projeto</h2>

        <nav class="box">
            <div class="level">
                <div class="level-left">
                    <div class="level-item">
                        <h1 class="title is-5">Processo do {{ $process->periodTitle }}</h1>
                    </div>
                </div>

                <div class="level-right">
                    <p class="level-item">
                        <span class="tag is-{{ $process->isOpened() ? 'success' :
                                                   ($process->isFinished() ? 'danger' : 'warning') }}">
                            {{ $process->isOpened() ? 'Aberto' :
                                   ($process->isFinished() ? 'Terminado' : 'Fechado') }}
                        </span>
                    </p>

                    <p class="level-item">
                        <span>
                            <i class="fa fa-calendar"></i>
                            @if ($process->open_date->month == $process->close_date->month)
                                de @date($process->open_date, '%d') a @date($process->close_date)
                            @else
                                de @date($process->open_date, '%d de %B') a @date($process->close_date)
                            @endif
                        </span>
                    </p>
                </div>
            </div>

            <div class="level">
                <div class="level-left">
                    <div class="level-item">
                        <span class="icon is-small" style="margin-right: 0.3em">
                            <i class="fa fa-users"></i>
                        </span>
                        Total de inscritos: {{ $process->applications_count }}
                    </div>
                </div>

                <div class="level-right">
                    <nav class="pagination">
                        <a class="pagination-previous" rel="prev"
                           {{ $prevProcess ? '' : 'disabled' }}
                           href="{{ $prevProcess ? route('selection-process.show', $prevProcess) : '#' }}">
                            &laquo; Anterior
                        </a>
                        <a class="pagination-next" rel="next"
                           {{ $nextProcess ? '' : 'disabled' }}
                           href="{{ $nextProcess ? route('selection-process.show', $nextProcess) : '#' }}">
                            Próximo &raquo;
                        </a>
                    </nav>
                </div>
            </div>
        </nav>

        <div class="is-spaced-1 is-spaced-bottom-1">
            <p>
                Clique nos nomes dos candidatos para ver as respostas do formulário de candidatura.
                Para baixar um compilado das informações de contato dos candidatos,
                @if(Request::query('area'))
                    <a href="{{ route('selection-process.csv', [
                        'process' => $process,
                        'area' => Request::query('area')]) }}">
                        clique aqui
                    </a>
                @else
                    <a href="{{ route('selection-process.csv', $process) }}">
                        clique aqui
                    </a>
                @endif
                (apenas as candidaturas da área selecionada serão exportadas).
            </p>
        </div>

        <div class="tabs is-centered is-boxed">
            <ul>
                <li class="@active('*?area=*','is-active','negate')">
                    <a href="{{ route('selection-process.show', $process) }}">Todas</a>
                </li>
                @foreach($areas as $area)
                    <li class="@active('*$area->slug*')">
                        <a href="{{ route('selection-process.show', [$process, 'area' => $area->slug]) }}">{{ $area->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <candidates-table
                candidates-path="{{ route('selection-process.application.index', $process) }}"
                can-approve="{{ Gate::allows('approve-candidates') }}"
                can-reset="{{ Gate::allows('reset-candidates') }}"
        ></candidates-table>

        {{-- Don't even send the dialogs to the front-end if user is not authorized. --}}
        @can('approve-candidates')
            <candidates-dialogs :areas="{{ $areas }}"></candidates-dialogs>
        @endcan
    </section>
@endsection