@extends('layouts.member.base')
@section('title', 'Editando Perfil - Curso Exato')

@section('app-content')
    {{-- Top profile section --}}
    <section class="section">
        <div class="column">
            <div class="columns is-vcentered">
                <div class="column is-narrow">
                    <div class="image is-128x128">
                        <img src="{{ asset('img/user128x128.png') }}">
                    </div>
                </div>
                <div class="column profile-name">
                    <h1 class="title">
                        <a href="{{ route('member.show', $member) }}">{{ $member->name }}</a>
                    </h1>
                    @foreach($member->jobs as $job)
                        <p>
                            {{ $job->name }} de {{ $job->area_name }}
                            <span class="since">desde @date($job->pivot->created_at)</span>
                        </p>
                    @endforeach
                </div>
            </div>
        </div>

        <member-edit-form :member="{{ $member }}">
        </member-edit-form>
    </section>
@endsection
