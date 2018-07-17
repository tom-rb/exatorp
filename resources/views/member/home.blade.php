@extends('layouts.member.base')
@section('title', 'Curso Exato - Início')

@section('app-content')
    <section class="section">

        <h1 class="title is-3">Início</h1>
        <h2 class="subtitle is-5">
            Um olhar geral sobre todas as atividades
        </h2>

        <div class="box">
            <div class="content">
                @if(Auth::user()->isActive())
                    {{-- Active member --}}
                    <p>Olá gente,</p>
                    <p>Está tudo muito em construção ainda, me chamem lá no fórum qualquer coisa!</p>
                    <p>Antonio Ruby Barreto</p>
                @elseif(Auth::user()->isCandidate())
                    {{-- Candidate for member --}}
                    @if($application->status == \App\SelectionProcess\MemberApplication::REJECTED
                     || $application->status == \App\SelectionProcess\MemberApplication::ON_HOLD)
                        <p>Olá,</p>
                        <p>As inscrições para o Curso Exato acontecem no início dos semestres, fique ligado(a)!</p>
                    @else
                        <p>Seja bem vindo,</p>
                        <p>Espere o contato de nossa equipe para agendar a sua entrevista. Qualquer dúvida, por favor
                        envie um email para <a href="mailto:cursoexato@reitoria.unicamp.br">cursoexato@reitoria.unicamp.br</a></p>
                    @endif
                    <p>Equipe Exato</p>
                @else
                    {{-- Former member --}}
                    <p>Olá,</p>
                    <p>Que pena não a termos mais conosco! Caso queira entrar novamente as inscrições acontecem
                        como sempre, no início dos semestres.</p>
                    <p>Equipe Exato</p>
                @endif
            </div>
        </div>

    </section>
@endsection
