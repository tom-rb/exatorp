@extends('layouts.member.base')
@section('title', 'Candidatura - Curso Exato')

@push('head')
<style>
    .howdid {
        font-weight: 300;
        line-height: 1.4;
    }
</style>
@endpush

@php
    use \App\Members\LessonAvailability;
    use \App\SelectionProcess\MemberApplication;
@endphp

@section('app-content')
    <section class="section">
        @include('member._banner')

        <div class="level">
            <div class="level-item has-text-centered">
                <div>
                    <h3 class="heading title is-6">1ª escolha</h3>
                    <p class="title is-4 is-spaced-05">
                        {{ $vm->application->firstOption }}
                    </p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <h3 class="heading title is-6">2ª escolha</h3>
                    <p class="title is-4 is-spaced-05">
                        {{ $vm->application->secondOption or '-' }}
                    </p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <h3 class="heading title is-6">Como ficou sabendo?</h3>
                    <p class="howdid is-spaced-05">
                    @foreach($vm->howDidYouHearAnswers() as $answer)
                        {{ $answer }}{!! (!$loop->last && $loop->count > 1) ? ',<br>' : '' !!}
                    @endforeach
                    </p>
                </div>
            </div>
        </div>

        <div class="is-framed">
            <p>
                <span class="icon is-small is-spaced-right-05">
                    <i class="fa {{ [
                        MemberApplication::APPROVED => 'fa-check',
                        MemberApplication::ON_HOLD => 'fa-clock-o',
                        MemberApplication::REJECTED => 'fa-remove',
                        null => 'fa-handshake-o',
                    ][$vm->application->status] }}"></i>
                </span>
                {{ $vm->statusMessage() }}
                @if($vm->member->isActive())
                    <strong class="has-text-weight-semibold">
                        {{ $vm->approvedJob() }}
                    </strong>
                @endif
            </p>
        </div>

        <div class="is-framed">
            <h3 class="title is-4">Disponibilidade</h3>
            <div class="columns is-mobile is-multiline">
                @foreach(LessonAvailability::all() as $day => $slots)
                    <div class="column is-half-mobile is-one-third-tablet-only">
                        <p class="notification is-primary availability-header">
                            {{ dayOfWeekName($day) }}
                        </p>
                        <div class="notification availability-cell">
                            @foreach($slots as $slot)
                                <div class="field">
                                    <label class="b-checkbox checkbox">
                                        <input type="checkbox" disabled
                                               {{ $vm->member->availability->isAvailable($day,$slot) ? 'checked' : '' }}>
                                        <span class="check"></span>
                                        <span class="control-label">
                                            {{ LessonAvailability::slotDescription($slot) }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <h4 class="title is-5">Observações</h4>
            <p>{{ $vm->member->availability->observations() ?: 'Nenhuma.' }}</p>
        </div>

        <div class="is-framed">
            <h4 class="title is-4">Questões do processo</h4>
            @foreach($vm->process->questions as $key => $question)
                <h4 class="title is-5">{{ $question->question() }}</h4>
                <div class="content">
                    <p>{{ $vm->application->answers[$key] }}</p>
                </div>
            @endforeach
        </div>

    </section>
@endsection