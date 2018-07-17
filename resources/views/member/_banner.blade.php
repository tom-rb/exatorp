<div class="profile">
    <div class="columns is-desktop is-vcentered">
        <div class="column">
            <div class="columns is-vcentered">
                <div class="column is-narrow">
                    <div class="image is-128x128">
                        <img src="{{ asset('img/user128x128.png') }}">
                    </div>
                </div>
                <div class="column profile-name">
                    <h1 class="title">
                        <a href="{{ route('member.show', $vm->member) }}">{{ $vm->member->name }}</a>
                    </h1>
                    @foreach($vm->member->jobs as $job)
                    <p>
                        {{ $job->name }} de {{ $job->area_name }}
                        <span class="since">desde @date($job->pivot->created_at)</span>
                    </p>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="column">
            <div class="columns is-vcentered">
                <div class="column">
                    <p><strong>Email</strong> {{ $vm->member->email }}</p>
                    @if(!is_null($vm->member->phones))
                        <p><strong>Telefones</strong>
                            @foreach($vm->member->phones as $phone)
                                {{ $phone }}<br/>
                            @endforeach
                        </p>
                    @endif
                </div>
                <div class="column">
                    <p><strong>Curso</strong> {{ $vm->member->course }}</p>
                    <p><strong>Entrou em</strong> {{ $vm->member->admission_year }}</p>
                    <p><strong>RA</strong> {{ $vm->member->ra }}</p>
                </div>
            </div>
        </div>
    </div>
</div>