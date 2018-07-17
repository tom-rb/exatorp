@extends('layouts.member.base')
@section('title', 'Membros - Curso Exato')

@section('app-content')
    <section class="section">
        <h1 class="heading title is-4">Membros</h1>
        <h2 class="subtitle is-6">Nossos queridos voluntários</h2>

        <nav class="box">
            <div class="level">

                <div class="level-left">
                    <div class="level-item">
                        <p class="subtitle is-6">
                            @if(query_is_empty())
                                Somos <strong>{{ $members->total() }}</strong> membros atualmente
                            @elseif(query_has('antigos'))
                                Temos <strong>{{ $members->total() }}</strong> ex-membros
                            @elseif(query_has('esperando'))
                                Temos <strong>{{ $members->total() }}</strong> membros em espera de oportunidade
                            @endif
                        </p>
                    </div>
                </div>

                <div class="level-right">
                    <p class="level-item">
                        @if(query_is_empty())
                            <span class="has-text-bold">Ativos</span>
                        @else
                            <a href="{{ route('member.index') }}">Ativos</a>
                        @endif
                    </p>
                    <p class="level-item">
                        @if(query_has('antigos'))
                            <span class="has-text-bold">Ex-membros</span>
                        @else
                            <a href="{{ route('member.index', ['status' => 'antigos']) }}">Ex-membros</a>
                        @endif
                    </p>
                    <p class="level-item">
                        @if(query_has('esperando'))
                            <span class="has-text-bold">Em espera</span>
                        @else
                            <a href="{{ route('member.index', ['status' => 'esperando']) }}">Em espera</a>
                        @endif
                    </p>
                </div>

            </div>
        </nav>

        <table class="table is-fullwidth">
            <thead>
            <tr>
                <th>Nome</th>
                <th>Área</th>
                <th>Cargo</th>
                <th>E-mail</th>
            </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                    <tr class="is-vcentered">
                        <td><a href="{{ route('member.show', $member) }}">{{ $member->name }}</a></td>
                        <td>
                            @foreach($member->jobs as $job)@if(!$loop->first),<br/>@endif{{ $job->area_name }}@endforeach
                        </td>
                        <td>
                            @foreach($member->jobs as $job)@if(!$loop->first),<br/>@endif{{ $job->name }}@endforeach
                        </td>
                        <td>{{ $member->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($members->total() > 0)
            {{ $members->appends(request()->query())->links() }}
        @else
            <p class="has-text-centered">Nenhum resultado.</p>
        @endif
    </section>
@endsection
