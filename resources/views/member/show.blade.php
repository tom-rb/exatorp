@extends('layouts.member.base')
@section('title', 'Perfil - Curso Exato')

@section('app-content')
    {{-- Admin Impersonate button --}}
    @can('impersonate', $vm->member)
        <a class="button is-warning is-pulled-right" href="{{ route('impersonate.start', $vm->member) }}">
            <span class="icon"><i class="fa fa-user-circle-o"></i></span>
            <span>Atuar como</span>
        </a>
    @endcan

    {{-- Top profile section --}}
    <section class="section">
        @include('member._banner')

        @can('update', $vm->member)
            <div class="level">
                <div class="level-left"></div>
                <div class="level-right">
                    <p class="level-item">
                        <a class="button is-primary is-small"
                           href="{{ route('member.edit', $vm->member) }}">Editar perfil</a>
                    </p>
                    @can('dismiss', $vm->member)
                        <p class="level-item">
                            <confirmation-button v-cloak
                                    class="is-small"
                                    title="Desligando {{ $vm->member->name }}"
                                    message="Essa ação irá <strong>desligar a pessoa do Exato</strong>.
                                    Ao confirmar, a pessoa ficará marcada como &quot;Ex-membro&quot; e o
                                    acesso ao sistema será extremamente restrito."
                                    type="danger"
                                    ok-label="Desligar Pessoa"
                                    :autofocus-cancel="true"
                                    route="{{ route('member.dismiss', $vm->member) }}"
                                    method="post"
                            >Desligar</confirmation-button>
                        </p>
                    @endcan
                </div>
            </div>
        @endcan

        <div class="box">
            <div class="content">
                <p>{{ $vm->statusMessage() }}</p>
            </div>
        </div>
    </section>
@endsection
